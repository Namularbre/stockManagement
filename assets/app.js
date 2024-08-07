import {Application} from "./vendor/@hotwired/stimulus/stimulus.index.js";
import DeleteController from "./controllers/deleteController.js";
import FinishAlertController from "./controllers/finishAlertController.js";
/*import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';*/

const application = Application.start();
application.register("delete", DeleteController);
application.register("finish", FinishAlertController);
