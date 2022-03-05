# Sample CakePHP App Using Action class

This is a sample application that uses action class
instead of controller class.
**Keep in mind the code is just for education purpose
and was not fully tested**

In src/Application::handle we use a custom factory to get
action class and call execute method to return a response object.

See:

- [Application::handle](./src/Application.php#L48) - Modify default behavior to not use controller class.
- [ActionFactory](./src/Controller/ActionFactory.php) - Create an action class based on the request.
- [Base Action](./src/Controller/Action.php) - Base action with some methods to render templates.
- [DisplayAction](./src/Controller/Action/Pages/DisplayAction.php) - Replace PagesController::display method
