from discord.ext import commands
from discord.utils import get
import discord
import mysql.connector
import random

db = mysql.connector.connect(
    host = "127.0.0.1",
    user = "root",
    password = "root",
    database = "DB_NAME"
)

intents = discord.Intents().all()
client = commands.Bot(command_prefix='^', intents=intents)

@client.command()
async def verify(ctx, *, code: str = None):
    verifiedRole = discord.utils.get(ctx.guild.roles, name = 'Verified')
    if verifiedRole in ctx.author.roles:
        await ctx.send("You are already verified")
        return

    if code is None or len(code) != 5:
        await ctx.send("Invalid code. The code must be exactly 5 characters long.")
        return

    cursor = db.cursor()
    cursor.execute("SELECT username FROM players WHERE discordCode = %s", (code,))
    result = cursor.fetchone()

    if result:
        # Code matched a player in the database
        username = result[0]
        discordId = ctx.author.id
        discordCode = ""

        # Generate random 5 char discord code
        characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
        for _ in range(5):
            discordCode += characters[random.randint(0, len(characters) - 1)]
        
        # Update the database
        cursor.execute("UPDATE players SET discordId = %s, discordCode = %s WHERE username = %s", (discordId, discordCode, username))
        db.commit()
        await ctx.author.add_roles(verifiedRole)
        await ctx.send(f"Verification successful for ign: {username}")
    else:
        # Code didn't match any player in the database
        await ctx.send("Invalid code. Code not found in the database.")

    cursor.close()


@client.command()
async def check(ctx, member: discord.Member = None):
    # Get the verified role object
    verifiedRole = discord.utils.get(ctx.guild.roles, name = 'Verified')

    # If no member is mentioned, default to the author of the command
    if member is None:
        member = ctx.author

    # Check if the member has the verified role
    if verifiedRole in member.roles:
        cursor = db.cursor()
        cursor.execute("SELECT username FROM players WHERE discordId = %s", (member.id,))
        result = cursor.fetchone()

        if result:
            await ctx.send(f'{member.mention}\u200B found with username: {result[0]}')
        else:
            await ctx.send(f'{member.mention}\u200B has no Discord ID in the database.')
    else:
        await ctx.send(f'{member.mention}\u200B is not verified.')

client.run('BOT_TOKEN')
