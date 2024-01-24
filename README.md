# symfony/ux-icons

## Setup

If planning on using the [`defer`](#deferred-icons) option, add the following to your base
template (`templates/base.html.twig`), right before the closing `</body>` tag:

```twig
<twig:Icon:DeferredStack />
```

## Add Icons

No icons are provided by this package. Add your svg icons to the `templates/icons/` directory.
The name of the file is used as the name of the icon (`name.svg` will be named `name`).

When icons are rendered, the class attribute on the original `<svg>` element will be removed.
This allows you to copy/paste icons from sites like [heroicons.com](https://heroicons.com/) and
not worry about hard-coded classes interfering with your design.

### Install Command

The [Blade UI Kit](https://blade-ui-kit.com/) has a huge searchable repository of icons from
many different icon sets. This package provides a command to locally install icons from this
site.

1. Visit [Blade UI Kit](https://blade-ui-kit.com/blade-icons#search) and search for an icon
   you'd like to use. Once you find one you'd like, visit the icon's profile page and note
   the name. For instance, https://blade-ui-kit.com/blade-icons/heroicon-s-user has the name
   `heroicon-s-user`.
2. Run the following command, replacing `heroicon-s-user` with the name of the icon you'd like
   to install:

    ```bash
    bin/console ux:icons:require heroicon-s-user # saved as `heroicon-s-user.svg`

    # adjust the local name
    bin/console ux:icons:require heroicon-s-user:user # saved as `user.svg`
    ```

## Usage

```html
<twig:Icon name="user-profile" class="w-4 h-4" />
```

### Deferred Icons

By default, icons are rendered inline. If you have a lot of duplicate icons on a page, you can
_defer_ the rendering of an icon. This will render the icon once and then reuse the rendered
icon for all other instances.

```html
<twig:Icon name="user-profile" class="w-4 h-4" defer />
```

> [!IMPORTANT]  
> The `<twig:Icon:DeferredStack />` component ([as shown above](#setup)) needs to be on the page
> for deferred icons to work.

### List Available Icon Names

```bash
bin/console ux:icons:list
```

## Caching

To avoid having to parse icon files on every request, icons are cached (`app.cache` by default).
During container warmup (`cache:warmup` and `cache:clear`), the icon cache is warmed.

If using a tagged cache adapter, cached icons are tagged with `ux-icon`.
