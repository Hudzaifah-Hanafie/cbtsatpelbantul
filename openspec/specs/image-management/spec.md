# image-management Specification

## Purpose
TBD - created by archiving change add-image-upload-to-questions-and-options. Update Purpose after archive.
## Requirements
### Requirement: Image upload and storage
The system **SHALL** be able to receive, validate, and store image files uploaded by the admin for question and option entities.

#### Scenario: Successful image upload
- **WHEN** Admin uploads an image file (jpg/jpeg/png/webp) with size < 5MB
- **THEN** The system **SHALL** store the file in public storage and record its path in the database

#### Scenario: Failed upload due to size
- **WHEN** Admin uploads an image file with size > 5MB
- **THEN** The system **SHALL** reject the file and display a validation error message

#### Scenario: Failed upload due to format
- **WHEN** Admin uploads a non-image file (e.g., pdf or exe)
- **THEN** The system **SHALL** reject the file and display an unsupported format error message

### Requirement: Image removal
The system **SHALL** be able to delete physical files from storage when the Admin chooses to remove an image from a question/option or when the question/option is permanently deleted.

#### Scenario: Delete image reference
- **WHEN** Admin clicks the remove image button on a question row in the Bulk Editor
- **THEN** The system **SHALL** delete the file from storage and clear the image_path column in the database

