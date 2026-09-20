=== Product Document Viewer for WooCommerce ===
Contributors: coderalamin
Donate link: https://almn.me
Tags: woocommerce, document preview, pdf preview, product preview, file preview
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Preview PDFs, Word, Excel, and PowerPoint files directly on WooCommerce product pages.

== Description ==

**Product Document Viewer for WooCommerce** adds a simple document preview button to your WooCommerce store, allowing customers to view supported documents directly from the product page before making a purchase.

It's ideal for **books, ebooks, digital products, catalogs, brochures, product guides, course materials, sample chapters, and other document-based products**.

Instead of asking customers to download a file or leave the product page, you can give them a quick preview of the content and help them make a more informed purchasing decision.

PDFs are previewed using the browser's own built-in viewer. Word, Excel, and PowerPoint files are previewed using Microsoft's Office Online viewer.

= Key Features =

* **PDF Preview** — Let customers preview PDF files directly from your product pages.
* **Word Document Preview** — Support for Microsoft Word documents.
* **Excel Preview** — Preview Excel spreadsheets before purchase.
* **PowerPoint Preview** — Display PowerPoint presentations directly from the product page.
* **Multiple Files Per Product** — Attach more than one document to a product; customers browse them in a single preview window.
* **Preview Button** — Add a clear preview button to your WooCommerce products.
* **Shortcode Support** — Use `[pdvwc_preview_button]` to place the preview button anywhere you need.
* **WooCommerce Integration** — Designed specifically for WooCommerce product pages.
* **Customizable Styling** — Customize the preview button appearance and placement from the plugin settings.
* **Simple Setup** — Add your document, configure the preview, and let your customers view it.

= Perfect For =

* Books and ebooks
* Sample chapters
* Digital products
* Product catalogs
* Brochures
* Course materials
* Product documentation
* Reports and guides
* Downloadable resources

= How It Works =

1. Add a supported document to your WooCommerce product.
2. Configure the preview settings.
3. A preview button is displayed on the product page.
4. Customers can preview the document before deciding to purchase.

The preview opens without requiring customers to download the original file first.

== Installation ==

= From the WordPress Dashboard =

1. Log in to your WordPress dashboard.
2. Go to **Plugins > Add New Plugin**.
3. Search for **Product Document Viewer for WooCommerce**.
4. Click **Install Now**.
5. Click **Activate**.

= Manual Installation =

1. Download the plugin ZIP file.
2. Go to **Plugins > Add New Plugin > Upload Plugin**.
3. Select the plugin ZIP file.
4. Click **Install Now**.
5. Activate the plugin.

= Configuration =

1. Make sure WooCommerce is installed and activated.
2. Edit a WooCommerce product.
3. Add the document(s) you want customers to preview under the "Document Preview Files" field.
4. Open **WooCommerce > Document Viewer** to configure the preview button, position, and styling.
5. Visit the product page and test the preview.

== Frequently Asked Questions ==

= What file types are supported? =

The plugin supports PDF, Microsoft Word, Excel, and PowerPoint documents.

= Can I customize the preview button? =

Yes. You can customize the button styling and position from **WooCommerce > Document Viewer**.

= Can I use a shortcode to place the preview button? =

Yes. Use the `[pdvwc_preview_button]` shortcode to display the preview button anywhere supported by your theme's layout. You can optionally pass specific attachment IDs: `[pdvwc_preview_button media_ids="123,456"]`.

= I upgraded from an older version — did my settings and uploaded files carry over? =

Yes. The plugin runs a one-time migration on upgrade that copies your existing button settings and any documents already attached to products into the new format automatically. No action is needed.

= Will it work with my WooCommerce theme? =

The plugin is designed to work with WooCommerce-compatible themes. Most themes should work without additional configuration, although heavily customized themes may require additional styling.

== Screenshots ==

1. Preview button on a WooCommerce product page.
2. Document preview opened in a lightbox.
3. Plugin settings page under WooCommerce.

== Changelog ==

= 1.3.0 =
* Rebranded from "Read a Little" to "Product Document Viewer for WooCommerce" with a consistent `pdvwc_` naming scheme throughout.
* Added a one-time automatic migration for existing settings and product preview files from the previous naming scheme.
* Fixed: multiple documents attached to one product now open in a single preview window with next/prev navigation, instead of rendering a separate button per file.
* Fixed: unchecking a settings checkbox (e.g. "Hide button") now actually saves as unchecked.
* Fixed: PDF previews no longer navigate away from the page instead of opening in the lightbox.
* Replaced the unofficial Google Docs Viewer with the browser's native PDF rendering and Microsoft's officially supported Office Online viewer for Word/Excel/PowerPoint files.
* Removed a hardcoded third-party fallback file; local/development environments now use a bundled sample file instead.
* Added the `pdvwc_button_font_size` and `pdvwc_button_border_width` settings fields, which existed in code previously but had no way to be configured.

= 1.2.2 =
* Internal maintenance release during rebranding.

= 1.2.1 =
* Initial public-facing release as "Document Viewer for WooCommerce".

== Upgrade Notice ==

= 1.3.0 =
Rebrand and multiple bug fixes. Settings and existing product preview files are migrated automatically — no manual steps required.