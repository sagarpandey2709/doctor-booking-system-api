## Doctor Booking System

## Service Layer Pattern
## Description:
The Service Layer Pattern is a design pattern that organizes the business logic of an application into services, keeping it separate from the code that handles HTTP requests (controllers). In our system, this pattern allows us to create services that handle all appointment-related processes, such as booking appointments, updating their status, and checking for conflicts.

## How it Benefits the Project:
Separation of Concerns: The business logic for managing appointments, patients, and doctors is isolated in service classes, making the codebase cleaner and more maintainable.
Reusability: By organizing the logic into services, the code can be reused across multiple parts of the system, ensuring consistency and reducing duplication.
Testability: Services can be independently tested, which makes it easier to write unit tests and improve the overall quality of the system.

## Example in the System:
For example, when a patient books an appointment, the AppointmentService manages the logic of verifying the doctor's availability, checking appointment conflicts, and creating the booking in the system. This keeps the controller lightweight and focused solely on handling incoming requests and responses.

Account details

Patent login
email: Patient@gmail.com
password: password

Doctor login
email: Doctro@gmail.com
password: password

## cron job for email notification to patient 30 minutes before their appointment.


