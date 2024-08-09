import {Application} from "./vendor/@hotwired/stimulus/stimulus.index.js";
import DeleteController from "./controllers/deleteController.js";
import FinishAlertController from "./controllers/finishAlertController.js";
import PutController from "./controllers/putController.js";

const application = Application.start();
application.register("delete", DeleteController);
application.register("finish", FinishAlertController);
application.register("put", PutController);
