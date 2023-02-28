# Forms Changelog

## 1.1.4 - 2023-02-28
### Fixed
- Changed the lowercase "l" to an UperCase "L" to fix a Composer 2 compatibility issue.

## 1.1.1 - 2019-10-03

### Fixed
- Fixed plugin to be compatible with newer versions of Craft


## 1.1.0 - 2018-08-05 [CRITICAL]

### Changes
- No need to add the |raw filter anymore to twig tags.<br>
`{{ dolphiqForm('contact')|raw }}`<br>will become<br>  `{{ dolphiqForm('contact') }}`
- Fixed privacy issue
- Fixed case sensitive naming
- Use class instead of classname, for use in recent php versions
- Fixed returning the correct customer mail
- Fixed using the correct filename for the owner mail
## 1.0.0 - 2017-10-16
- Initial release.

### Features
- Easy out of the box client and server side validation with use of rules in a model.
- Assign field labels in your model to use them in multiple areas.
- Easily enable/disable the form in the settings.
- Easily enable/disable logging form entries into the database in the settings.
- Control the recipient and subject of the contact requests e-mails in the plugin settings per form.
- Twig extensions, form examples and E-mail examples.
