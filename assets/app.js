import './styles/app.css';
import {Application} from "./vendor/@hotwired/stimulus/stimulus.index.js";
import DeleteController from "./controllers/deleteController.js";

const application = Application.start();
application.register("delete", DeleteController);
