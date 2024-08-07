import {Application} from "./vendor/@hotwired/stimulus/stimulus.index.js";
import DeleteController from "./controllers/deleteController.js";
/*import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';*/

const application = Application.start();
application.register("delete", DeleteController);
