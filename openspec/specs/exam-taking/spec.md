# exam-taking Specification

## Purpose
TBD - created by archiving change add-image-upload-to-questions-and-options. Update Purpose after archive.
## Requirements
### Requirement: Responsive image rendering in test
The exam-taking page **SHALL** render question images and answer option images proportionally.

#### Scenario: Question with image
- **WHEN** Participant accesses a question page that has an image
- **THEN** The image **SHALL** be displayed above the question text with maximum width according to the container (responsive)

#### Scenario: Option with image
- **WHEN** An answer choice (option) has an image
- **THEN** The image **SHALL** be displayed within the answer choice label above or next to the option text

