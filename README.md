=== Document Viewer for WooCommerce ===
Contributors: coderalamin
Tags: woocommerce, product preview, document preview, pdf preview, file preview
Requires at least: 4.0
Tested up to: 7.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Preview PDFs, Word, Excel, and PowerPoint files directly on WooCommerce product pages.

== Description ==

**Document Viewer for WooCommerce** adds a simple document preview button to your WooCommerce store, allowing customers to view supported documents directly from the product page before making a purchase.

It's ideal for **books, ebooks, digital products, catalogs, brochures, product guides, course materials, sample chapters, and other document-based products**.

Instead of asking customers to download a file or leave the product page, you can give them a quick preview of the content and help them make a more informed purchasing decision.

The plugin uses Google Docs Viewer to display supported documents in a convenient preview window.

== Key Features ==

* **PDF Preview** — Let customers preview PDF files directly from your product pages.
* **Word Document Preview** — Support for Microsoft Word documents.
* **Excel Preview** — Preview Excel spreadsheets before purchase.
* **PowerPoint Preview** — Display PowerPoint presentations directly from the product page.
* **Preview Button** — Add a clear preview button to your WooCommerce products.
* **Shortcode Support** — Use `[read_little_button]` to place the preview button anywhere you need. `[product_preview_button]` is also supported as an alias.
* **WooCommerce Integration** — Designed specifically for WooCommerce product pages.
* **Customizable Styling** — Customize the preview button appearance and placement from the plugin settings.
* **Simple Setup** — Add your document, configure the preview, and let your customers view it.

== Perfect For ==

Document Viewer for WooCommerce is especially useful for:

* Books and ebooks
* Sample chapters
* Digital products
* Product catalogs
* Brochures
* Course materials
* Product documentation
* Reports and guides
* Downloadable resources

== How It Works ==

1. Add a supported document to your WooCommerce product.
2. Configure the preview settings.
3. A **Preview** button is displayed on the product page.
4. Customers can preview the document before deciding to purchase.

The preview opens without requiring customers to download the original file first.

== Installation ==

= From the WordPress Dashboard =

1. Log in to your WordPress dashboard.
2. Go to **Plugins > Add New Plugin**.
3. Search for **Document Viewer for WooCommerce**.
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
3. Add the document you want customers to preview.
4. Open the plugin settings to configure the preview button, position, supported file types, and styling.
5. Visit the product page and test the preview.

== Frequently Asked Questions ==

= What file types are supported? =

The plugin supports PDF, Microsoft Word, Excel, and PowerPoint documents.

= Can I customize the preview button? =

Yes. You can customize the button styling and position from the plugin settings.

= Can I use a shortcode to place the preview button? =

Yes. Use the `[read_little_button]` shortcode to display the preview button anywhere supported by your WooCommerce layout. `[product_preview_button]` works identically as an alias.

= Will it work with my WooCommerce theme? =

The plugin is designed to work with WooCommerce-compatible themes. Most themes should work without additional configuration, although heavily customized themes may require additional styling.

= Can customers preview a document without downloading it? =

Yes. Supported documents can be displayed in the preview viewer directly from the product page.

= Does the plugin work without WooCommerce? =

No. Document Viewer for WooCommerce requires WooCommerce to be installed and activated.

== External services ==

This plugin uses Google Docs Viewer to render in-browser previews of PDF, Word, Excel, and PowerPoint files that are attached to a product. It is needed because browsers cannot natively preview these file formats inline.

It sends the file's URL to Google's viewer whenever a shopper opens the preview button on a product page with one of these file types attached. No other product, customer, or site data is sent.
This service is provided by Google: [Terms of Service](https://policies.google.com/terms), [Privacy Policy](https://policies.google.com/privacy).

On local/development environments (where a public URL isn't available for Google's viewer to fetch), the plugin substitutes a static sample document hosted at shaliktheme.com so the preview UI can still be tested. No customer or site data is sent to shaliktheme.com; it only serves a static demo file.

== Screenshots ==

1. **Plugin Settings** — Configure preview behavior, button position, and styling.
2. **Product Edit Screen** — Configure document previews for your WooCommerce products.
3. **Preview Button** — Customers can access the preview directly from the product page.
4. **Document Preview** — Display supported documents in a convenient preview window.

== Changelog ==

= 1.2.1 =
* Reverted the plugin name to **Read a Little for WooCommerce** — the "WooCommerce Product Preview" rebrand in 1.2.0 led with a third-party trademark and was flagged by the WordPress.org review team.
* Fixed: Sanitize callbacks added to all registered settings.
* Fixed: Escaped output in the shortcode's no-preview fallback message.
* Fixed: Text domain now matches the plugin slug (read-little).
* Fixed: Version constant that was still hardcoded to 1.1.0 despite later version bumps.
* Fixed: Unified the two different hardcoded localhost demo files into one.
* Added: Disclosure of the Google Docs Viewer external service.

= 1.2.0 =
* Improved plugin description and documentation.
* Added the `[product_preview_button]` shortcode.
* Retained `[read_little_button]` for backwards compatibility.

= 1.1.0 =
* Added support for PDF, Excel, and Word document previews.
* Added the `[read_little_button]` shortcode for flexible button placement.
* Fixed margin issues after image previews.

= 1.0.0 =
* Initial release.
* Added basic document preview functionality and button customization options.

== Upgrade Notice ==

= 1.2.1 =
* Reverts the plugin name to Document Viewer for WooCommerce per WordPress.org's review feedback, and fixes the underlying security/sanitization issues from that review.

== License ==

Document Viewer for WooCommerce is licensed under the GPLv2 or later.

See the [GNU General Public License](https://www.gnu.org/licenses/gpl-2.0.html) for more details.