# PinBlooms Google Rich Snippets

## Overview
**PinBlooms_GoogleRichSnippets** is a powerful Magento 2 extension designed to improve your search engine rankings with easy-to-set-up Google rich snippets. By adding structured data to your store pages, you can highlight key information in search results, increase visibility, and attract highly targeted traffic.

## Features
✅ **Product Snippets:** Display product details such as reviews, ratings, availability, and pricing in search results.  
✅ **Organization Snippets:** Configure company details like business name, logo, description, and social media links.  
✅ **Category Snippets:** Show category images and descriptions to enhance search visibility.  
✅ **FAQ Schema Markup:** Add FAQ schema to product pages, categories, CMS pages, and blog posts.  
✅ **HowTo Markup:** Easily add step-by-step instructions to store pages without coding.  
✅ **Search Box & Breadcrumbs:** Enhance user navigation with a built-in search box and breadcrumb markup in search results.  
✅ **Product Video Markup:** Include structured data for product videos to enhance search listings.

## Installation
1. Download or clone this repository.
2. Copy the module files to `app/code/PinBlooms/GoogleRichSnippets`.
3. Run the following commands in the Magento root directory:

```sh
php bin/magento module:enable PinBlooms_GoogleRichSnippets
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:flush
```

## Configuration
1. Navigate to **Admin Panel → Stores → Configuration → PinBlooms → Google Rich Snippets**.
2. Enable the required schema types for products, categories, and CMS pages.
3. Configure organization details like company name, logo, and social media links.
4. Save the configuration and clear the cache to apply changes.

## Benefits
- **Improved SEO**: Helps search engines understand your content and display rich snippets.
- **Higher Click-Through Rates**: Engaging search results attract more users.
- **Better User Experience**: Provides structured and informative search listings.
- **Easy to Configure**: No coding skills required, simple admin panel settings.

## Compatibility
- Compatible with Magento **2.x** (Magento 2.3.x, 2.4.x, and later versions).
- Supports **multi-store and multi-language** environments.

## Support
For any issues or feature requests, please reach out to our support team.

---
**Increase your store's search visibility with PinBlooms Google Rich Snippets today!** 🚀

