Installation of the framework is straightforward.

Create project folder (called project for short)
Create 'include' folder in the project
Create 'public' folder
Create 'admin' folder

Copy the MIPHPF in the include folder
Create own include directory (usualy the project name or abbreviation)

Create appropriate index scripts

For each page create a separate folder in 'public' or 'admin'
In the page folder should be placed all page templates (create, edit, listing etc.) and the controlling php script

When creating own widgets, validators and other derived frequently used classes put them in your include folder, and register them with the miLoader class.
