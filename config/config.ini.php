; <?php exit; ?> DO NOT REMOVE THIS LINE

; This file contains the default settings for RSS-Bridge. Do not change this
; file, it will be replaced on the next update of RSS-Bridge! You can specify
; your own configuration in 'config.ini.php' (copy this file).

[system]

; System environment: "dev" or "prod"
env = "dev"

; Only these bridges are available for feed production
; enabled_bridges[] = *
enabled_bridges[] = AmazonPriceTrackerBridge
enabled_bridges[] = ArbetsdomstolenBridge
enabled_bridges[] = BicyclingKristerIsakssonBridge
enabled_bridges[] = ComicsKingdomBridge
enabled_bridges[] = DacksnackBridge
enabled_bridges[] = DagensNyheterDirektBridge
enabled_bridges[] = FacebookBridge
enabled_bridges[] = FirefoxReleaseNotesBridge
enabled_bridges[] = GithubPackagesBridge
enabled_bridges[] = GoComicsBridge
enabled_bridges[] = GoogleSearchBridge
enabled_bridges[] = InstagramBridge
enabled_bridges[] = LudvikaKommunBridge
enabled_bridges[] = NotAlwaysBridge
enabled_bridges[] = RedditBridge
enabled_bridges[] = RegionDalarnaBridge
enabled_bridges[] = RegionStockholmBridge
enabled_bridges[] = SamsungMobileChangelogBridge
enabled_bridges[] = SiljanNewsBridge
enabled_bridges[] = SolnaStadBridge
enabled_bridges[] = SoundcloudBridge
enabled_bridges[] = SVTSnabbkollenBridge
enabled_bridges[] = TestFaktaBridge
enabled_bridges[] = TrafikverketRV5066LudvikaBridge
enabled_bridges[] = TwitterBridge
enabled_bridges[] = YoutubeBridge


; Defines the timezone used by RSS-Bridge
; Find a list of supported timezones at
; https://www.php.net/manual/en/timezones.php
; timezone = "UTC" (default)
timezone = "Europe/Stockholm"

; Display a system message to users.
message = ""

; Whether to enable maintenance mode. If enabled, feed requests receive 503 Service Unavailable
enable_maintenance_mode = false

; Max file size for simple_html_dom in bytes (10000000 => 10 MB)
max_file_size = 10000000

[http]

; Operation timeout in seconds
timeout = 5

; Operation retry count in case of curl error
retries = 1

; Curl user agent
; This is already set by curl-impersonate, which comes included as default
; in RSS-Bridge docker container. Use only if you know what you're doing.
; For reference, see https://github.com/lexiforest/curl-impersonate/tree/main/docs
;useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:102.0) Gecko/20100101 Firefox/102.0"

; Max http response size in MB
max_filesize = 20

[cache]

; Cache type: file, sqlite, memcached, array, null
type = "file"

; Allow users to specify custom timeout for specific requests.
; true  = enabled
; false = disabled (default)
custom_timeout = false

[logging]

;file_path = "/var/log/rss-bridge.log"
; DEBUG, INFO, WARNING or ERROR
;file_level = "INFO"

[admin]

; Advertise an email address where people can reach the administrator.
; This address is displayed on the main page, visible to everyone!
; ""    = Disabled (default)
email = ""

; Advertise a contact URL (can be any URL!) e.g. "https://t.me/elegantobjects"
telegram = ""

; Show Donation information for bridges if available.
; This will display a 'Donate' link on the bridge view
; and a "Donate" button in the HTML view of the bridges feed.
; true  = enabled (default)
; false = disabled
donations = true

[proxy]

; The HTTP proxy to tunnel requests through
; https://curl.se/libcurl/c/CURLOPT_PROXY.html
; ""    = Proxy disabled (default)
url = ""

; Sets the proxy name that is shown on the bridge instead of the proxy url.
; ""    = Show proxy url
name = "Hidden proxy name"

; Allow users to disable proxy usage for specific requests.
; true  = enabled
; false = disabled (default)
by_bridge = false

[webdriver]

; Sets the url of the webdriver or selenium server
selenium_server_url = "http://localhost:4444"

; Sets whether the browser should run in headless mode (no visible ui)
; true = enabled
; false = disabled (default)
headless = false

[authentication]

; Enables basic authentication for all requests to this RSS-Bridge instance.
;
; Warning: You'll have to upgrade existing feeds after enabling this option!
;
; true  = enabled
; false = disabled (default)
enable = false

username = "admin"

; The password cannot be the empty string if authentication is enabled.
password = ""

; Token authentication (URL)
token = ""

[error]

; Defines how error messages are returned by RSS-Bridge
;
; "feed" = As part of the feed (default)
; "http" = As HTTP error message
; "none" = No errors are reported
output = "feed"

; Defines how often an error must occur before it is reported to the user
report_limit = 1

; --- Cache specific configuration ---------------------------------------------

[FileCache]

; The root folder to store files in.
; "" = Use the cache folder in the repository (default)
path = ""
; Whether to actually delete files when purging. Can be useful to turn off to increase performance.
enable_purge = true

[SQLiteCache]

; Filepath of the sqlite db file
file = "cache.sqlite"
; Whether to actually delete data when purging
enable_purge = true
; Busy wait in ms before timing out
timeout = 5000

[MemcachedCache]

host = "localhost"
port = 11211

; --- Bridge specific configuration ------

[TelegramBridge]

; Max pages to fetch (1 page => 20 messages), min=1 max=100
max_pages = 1

[DiscogsBridge]

; Sets the personal access token for interactions with Discogs. When
; provided, images can be included in generated feeds.
;
; "" = no token used (default)
personal_access_token = ""

[InstagramBridge]
session_id = %sessionid from step 1%
ds_user_id = %ds_user_id from step 1%
cache_timeout = %cache timeout in seconds%
