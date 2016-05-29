This custom Kirby field allows you to clone a page in the panel.

**Note: this field is in alpha state and just a proof of concept.**

## Installation

Put the field into `/site/fields`; create the folder if it does not exist yet.

Your structure should then look like this:

```
site/
  fields/
    duplicate/
      assets/
        duplicate.php
```

## In your blueprint

```yaml
fields:
  clone:
    type: duplicate
    placeholder: Enter a page title and press Enter …
    buttontext: Clone page
```

It's probably best to put this field before any other fields at the top of the form (or at the bottom).

You can provide a placeholder text and a buttontext. The examples above are the default settings.

This will create a button in your Panel form.

## Usage

1. Click on the "Clone Page" button => an input field is shown.
2. Enter the title of the new page into the input field and press `ENTER`.
3. If all is well and the page does not exist yet, the new page is created.

## Limitations

- If you are on a multi-lingual installation, the field is only shown in the default language. It does, however, create a page in every available language.
- The title entered into the input field will be used for all languages
- If you use the `URL-key` field in multi-lingual installations, make sure to change the value of this field in your new pages to prevent errors.

## Author

Sonja Broda info@texniq.de

## Feedback/issues

Pls. create an issue.
