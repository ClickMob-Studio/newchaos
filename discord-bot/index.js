const fetch = require('node-fetch');

const Discord = require("discord.js");
const config = require("./config.json");

const client = new Discord.Client({
    intents: ["GUILDS", "GUILD_MESSAGES"]
});

const prefix = "!";


client.on("messageCreate", async message => {
    console.log(message);

    if (message.author.bot) return;
    if (!message.content.startsWith(prefix)) return;

    const commandBody = message.content.slice(prefix.length);
    const args = commandBody.split(' ');
    const command = args.shift().toLowerCase();

    if (command === "stats") {

        const params = new URLSearchParams();
        params.append('id', 174);

        const data = await fetch('https://themafialife.com/api/fetchUser.php', {
            method: 'POST',
            body: params
        }).then(response => response.json());

        // console.log(data.name)

        const embed = new Discord.MessageEmbed()
        .setColor(6221193)
        // .setTitle('Some title')
        // .setURL('https://discord.js.org/')
        // .setAuthor({ name: 'Some name', iconURL: 'https://i.imgur.com/AfFp7pu.png', url: 'https://discord.js.org' })
        // .setDescription('Some description here')
        .setThumbnail(data.avatar)
        .addFields(
            { name: 'Name', value: data.name, inline: true },
            { name: 'Level', value: data.level, inline: true },
        )
        .setTimestamp();

        // const embed = {
        //     "content" : "-",
        //     "color": 6221193,
        //     "title" : "-",
        //     "thumbnail": {
        //         "url": data.avatar
        //     },
        //     "author": {
        //         "name": "Player Stats",
        //         "url": "-"
        //     },
        //     "fields": [{
        //             "name": "Name",
        //             "value": data.username,
        //             "inline": true
        //         },
        //         {
        //             "name": "Level",
        //             "value": data.level,
        //             "inline": true
        //         }
        //         // {
        //         //     "name": "Points",
        //         //     "value": "22,000",
        //         //     "inline": true
        //         // }
        //     ]
        // };
        message.channel.send({embeds: [embed]})
    }

});

//client.on("messageCreate", function (message) {
client.on('interactionCreate', async interaction => {
    console.log(interaction);
    if (interaction.message.author.bot) return;
    if (!interaction.message.content.startsWith(prefix)) return;

    const commandBody = interaction.message.content.slice(prefix.length);
    const args = commandBody.split(' ');
    const command = args.shift().toLowerCase();

    if (command === "stats") {

        interaction.message.reply('Test');

        await interaction.deferReply();

        const response = await fetch('https://themafialife.com/api/fetchUser.php', {
            method: 'POST',
            body: 'id=174'
        });
        const data = await response.json();

        const embed = {
            "color": 6221193,
            "thumbnail": {
                "url": data.avatar
            },
            "author": {
                "name": "Player Stats",
                "url": ""
            },
            "fields": [{
                    "name": "Name",
                    "value": data.username,
                    "inline": true
                },
                {
                    "name": "Level",
                    "value": data.level,
                    "inline": true
                }
                // {
                //     "name": "Points",
                //     "value": "22,000",
                //     "inline": true
                // }
            ]
        };
        // channel.send({
        //     embed
        // });

        message.reply({
            embed
        });
    }

    //   else if (command === "sum") {
    //     const numArgs = args.map(x => parseFloat(x));
    //     const sum = numArgs.reduce((counter, x) => counter += x);
    //     message.reply(`The sum of all the arguments you provided is ${sum}!`);
    //   }
});

client.login(config.BOT_TOKEN);