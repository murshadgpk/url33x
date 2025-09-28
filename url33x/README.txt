Clickable Link Creator (PHP, hosting-friendly)
Files:
- index.php : Form to create short links with image, description, timer.
- redirect.php : Landing page that shows image/desc and redirects after timer.
- uploads/ : Folder where uploaded images are saved.
- links.json : Stores mapping id => data
- .htaccess : Clean URLs (domain.com/abc123)
Instructions:
1. Upload all files and 'uploads' folder to public_html on your hosting.
2. Ensure uploads/ is writable (chmod 755 or 775).
3. Visit your domain, create a link, then share the short link on Facebook.
Note: Facebook caches preview; to refresh use FB Sharing Debugger.
