# AveForms Plugin

AveForms is a WordPress plugin for creating and managing custom contact forms using shortcodes and a custom post type.

## Features

- Create forms using a custom post type (`Aveform`)
- Add forms to any page or post using shortcodes
- Supports text, email, textarea inputs, and submit buttons
- Built-in validation with custom error messages
- AJAX form submission

## Installation

1. Copy the `aveforms` folder to your WordPress site's `wp-content/plugins/` directory.
2. In your WordPress admin, go to **Plugins** and activate **AveForms**.

## Creating a Form

1. In the WordPress admin, go to **Aveforms** in the sidebar.
2. Click **Add New Form**.
3. Use the editor to define your form using the `[aveform]` shortcode and its child shortcodes.

### Example Form

```
[aveform form_id="theform"]
  [aveform_input type="text" name="first_name" label="First Name" placeholder="First Name" rules="required{The field is required}|string{The value must be a string}|max:255{Maximum number of characters reached}|min:3{Minimum number of characters not met}"]
  [aveform_input type="text" name="last_name" label="Last Name" placeholder="Last Name" rules="required|string|max:255|min:3"]
  [aveform_input type="email" name="email" label="Email" placeholder="email@email.com" rules="required|email"]
  [aveform_textarea name="message" cols="5" rows="10" label="Message" placeholder="Type your message here" rules="required|min:10|max:500"]
  [aveform_submit text="Send" class="button button-primary"]
[/aveform]
```

## Displaying a Form

After publishing your form, copy the shortcode from the **Shortcode** column in the Aveforms list (e.g., `[aveformshow id="123"]`).

Paste this shortcode into any page or post where you want the form to appear.

## Shortcode Reference

### `[aveform]`

Wraps your form fields.

**Attributes:**
- `form_id` (string): HTML ID for the form.
- `form_class` (string): CSS class for the form.
- `title` (string): Form title.
- `description` (string): Form description.

### `[aveform_input]`

Adds a text/email input.

**Attributes:**
- `type` (text|email|etc): Input type.
- `name` (string): Field name (required).
- `label` (string): Field label.
- `placeholder` (string): Placeholder text.
- `rules` (string): Validation rules (see below).

### `[aveform_textarea]`

Adds a textarea input.

**Attributes:**
- `name` (string): Field name (required).
- `label` (string): Field label.
- `placeholder` (string): Placeholder text.
- `rows` (int): Number of rows.
- `cols` (int): Number of columns.
- `rules` (string): Validation rules.

### `[aveform_submit]`

Adds a submit button.

**Attributes:**
- `text` (string): Button text.
- `class` (string): CSS class.

## Validation Rules

- Separate rules with `|`
- Add custom error messages in `{}` after each rule

**Examples:**
- `required{This field is required}`
- `string{Must be a string}`
- `max:255{Too long}`
- `min:3{Too short}`

## AJAX Submission

Forms are submitted via AJAX. Success and error messages are displayed below the form.

## Customization

- CSS: Edit `assets/css/aveforms.css`
- JS: Edit `assets/js/aveforms.js`

## Support

For issues or feature requests, please contact the plugin author.