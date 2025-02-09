# tzchz/TOTPhp
A neat 2FA - TOTP Authenticator for personal use written in PHP.
### Initialization
Visit ./danger_zone/reinstall.php to install sqlite database.

Note that public access permissions of ./danger_zone directory as well as the sqlite.db file are supposed to be denied.
### Identification
This package is not loaded with a user-login system.

Make sure that only you can access the app.

We would suggest using .htpasswd along with CloudFlare Access for enhanced security.

Tips: You might need to revoke .htaccess protection temporarily in order to install the App as PWA if so.
### Danger Zone
For security, the option to remove an added 2FA Key and to reset your database are put separately in the ./danger_zone directory. Visit the page for directions.

We STRONGLY RECOMMEND you to REMOVE OR BLOCK this directory unless needed.
### What's more
### Using a Legacy Device?
Switch to the [Hex version](https://github.com/tzchz/TOTPhp_Hex) if you are using a Key with older Hex rules, which usually appears on former HARDWARE devices.

Legacy Keys should consist of Hex numbers [0-9A-F] instead of [2-7A-Z]. Stay on this Normal version unless you have confirmed this.
### Need Enhanced Security?
This is the Basic version which fetches your TOTP code without downloading the raw Key onto your local device.

This would be secure enough to prevent leaking from your local device. Still, it will not encrypt the Key stored on your server end.

See the [Advanced version](https://github.com/tzchz/TOTPhp_Advanced) if needed, which encrypts raw 2FA Keys in the server storage with the card you tap with your mobile device.
