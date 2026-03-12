# exam-management Specification

## Purpose
TBD - created by archiving change add-image-upload-to-questions-and-options. Update Purpose after archive.
## Requirements
### Requirement: Bulk Editor image support
The Bulk Editor interface **SHALL** provide a file input for each question item and each answer option.

#### Scenario: Display image preview
- **WHEN** Admin selects an image file in the file input on a question row
- **THEN** The interface **SHALL** display an instant image preview (using Alpine.js) before the form is saved

#### Scenario: Existing image display
- **WHEN** Admin opens the Manage Test page for an exam where questions already have images
- **THEN** The interface **SHALL** display the existing image on the relevant question/option row

