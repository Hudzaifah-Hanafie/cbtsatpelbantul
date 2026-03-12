## Why

The current CBT system only supports text for questions and options. Many exam types **MUST** have visual aids (like diagrams, graphs, or images) to make questions clear. Furthermore, this feature **SHALL** enable the creation of questions where participants must choose the correct image as their answer.

## What Changes

- **Database Update**: The system **SHALL** add an `image_path` column to the `questions` and `options` tables.
- **Dynamic Bulk Editor**: The system **SHALL** add a file input for each question and option row in the Admin Manage Test page.
- **Upload Validation**: The system **SHALL** implement file validation (Max 5MB, formats: jpg, jpeg, png, webp).
- **Storage System**: The system **SHALL** use Laravel's `public` disk to store image files.
- **Participant Exam View**: The system **SHALL** update the exam-taking page to render images above the question text or within the option label if an image is available.
- **Image Deletion Feature**: The system **SHALL** provide the ability to delete or replace an uploaded image.

## Capabilities

### New Capabilities
- `image-management`: The backend **MUST** handle the upload process, file validation, and file deletion from storage.

### Modified Capabilities
- `exam-management`: The Bulk Editor UI (Alpine.js) **MUST** support file inputs and dynamic image previews.
- `exam-taking`: The question display (Blade) **MUST** be responsive in rendering images for both questions and options.

## Impact

- **Models**: `Question` and `Option` (add `image_path` to fillable).
- **Controllers**: `AdminController` (file storage logic in `storeBulk`).
- **Views**: `admin.tests.manage` (file input UI) and `tests.show` (image rendering UI).
- **Storage**: Use of `storage/app/public/images` and the need to run `php artisan storage:link`.
