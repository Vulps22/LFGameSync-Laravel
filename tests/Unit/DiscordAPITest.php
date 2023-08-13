<?php
use App\Helpers\DiscordAPI;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiscordAPITest extends TestCase
{
    public function testGetUser()
    {
        // Mock the HTTP request
        Http::fake([
            'discord.com/api/users/@me' => Http::response([
                'id' => '123456789',
                'username' => 'test_user',
                'avatar' => '00000000000000000',
                'discriminator' => '0',
                'public_flags' => 64,
                'flags' => 64,
                'banner' => null,
                'accent_color' => 7812399,
                'global_name' => 'test_user',
                'avatar_decoration' => null,
                'banner_color' => '#77352f',
                'mfa_enabled' => true,
                'locale' => 'en-GB',
                'premium_type' => 0,
            ], 200),
        ]);

        // Call the getUser method
        $user = DiscordAPI::getUser('test_access_token');

        // Assert that the user object has the correct properties
        $this->assertEquals('123456789', $user['id']);
        $this->assertEquals('test_user', $user['username']);
        $this->assertEquals('00000000000000000', $user['avatar']);
        $this->assertEquals('0', $user['discriminator']);
    }

    public function testRefreshToken()
    {
        // Mock the HTTP request
        Http::fake([
            'discord.com/api/oauth2/token' => Http::response([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        // Call the refreshToken method
        $tokens = DiscordAPI::refreshToken('test_refresh_token');

        // Assert that the tokens array has the correct properties
        $this->assertEquals('new_access_token', $tokens['access_token']);
        $this->assertEquals('new_refresh_token', $tokens['refresh_token']);
        $this->assertEquals(3600, $tokens['expires_in']);
    }

    public function testGetGuilds()
    {
        // Mock the HTTP request
        Http::fake([
            'discord.com/api/users/@me/guilds' => Http::response([
                [
                    'id' => '123456789',
                    'name' => 'test_guild',
                    'icon' => '00000000000000000',
                    'owner' => true,
                    'permissions' => 2147483647,
                ],
            ], 200),
        ]);

        // Call the getGuilds method
        $guilds = DiscordAPI::getGuilds('test_access_token');

        // Assert that the guilds array has the correct properties
        $this->assertCount(1, $guilds);
        $this->assertEquals('123456789', $guilds[0]['id']);
        $this->assertEquals('test_guild', $guilds[0]['name']);
        $this->assertEquals('00000000000000000', $guilds[0]['icon']);
        $this->assertTrue($guilds[0]['owner']);
        $this->assertEquals(2147483647, $guilds[0]['permissions']);
    }

    public function testGetAvatar()
    {
        // Call the getAvatar method
        $avatarUrl = DiscordAPI::getAvatar('123456789', '00000000000000000');

        // Assert that the avatar URL is correct
        $this->assertEquals('https://cdn.discordapp.com/avatars/123456789/00000000000000000.png', $avatarUrl);
    }
}

?>