# Simple Form Module for Joomla! CMS

This module creates custom forms based on a PHP array.


Syntax for creating a form:

```
    //please note, if you use a PHP version below 7.0 you have to use array() instead of []
    [
        [
            'element_type' => [
                'name' => 'Name'
            ]
        ],
        [
            'element_type' => 'Name' //you can use a shorthand notation as well
        ],
        [
            'element_type' => [
                'name' => 'Name*' //include asterisk in name to flag an input as required
            ]
        ],
        [
            'submit' => 'send' //make sure to always include a submit button
        ]
    ]
```

You can use json as well, as the form content will be eval'd:

```
    json_decode($form_as_array);
```



## Form Content Element Types
You can use the following input types:

### Text inputs
text, color, date, datetime, datetime-local, email, month, number, range, search, tel, time, url, week

### Textareas
textarea

### Radio buttons, Checkboxes, Selects
checkbox, radio, select

Example:
```
    //those element types require some values to be assigned like this:
    [
        [
            'element_type' => [
                'name' => 'name_of_input',
                'values' => [
                    //add your values here
                    '1',
                    '2',
                    '3'
                ]
            ]
        ]
    ]

```

### Buttons
submit, reset, button

### File uploads
file

Example:
```
    [
        [
            'file' => [
                "name" => "file_name",
                "maxSize" => "max_size_in_bytes",
                "allowedExtensions" => ["pdf", "zip", "rar"] //file extensions you want to allow to be uploaded
            ]
        ]
    ]
```

Make sure to set a valid folder as root directory for file uploads in module settings.

If no *allowedExtensions* are set, users will be able to upload all kind of files except for the following:

**Upload of files with extensions containing this strings will be blocked by default: 'php', 'html', 'htaccess', 'htpasswd'. So nobody will be able to to upload that type of files (including file extensions like php5 or php7 etc.) for security reasons.**

## Captcha
reCAPTCHA can be enabled in module settings (make sure you have valid keys in your reCAPTCHA system plugin settings).

## Email addresses
You add multiple recipients by adding multiple email addresses separated by commas.


