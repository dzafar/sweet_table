document.getElementById('loginBtn').onclick = () => {
    const clientId = '1254448516659417180'; // Замените на ваш Client ID
    const redirectUri = 'http://sweettable.local/';
    const scope = 'identify guilds';

    const authUrl = `https://discord.com/api/oauth2/authorize?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&response_type=code&scope=${encodeURIComponent(scope)}`;
    window.location.href = authUrl;
};

window.onload = () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('code')) {
        document.getElementById('guildsList').style.display = 'block';
    }
};
