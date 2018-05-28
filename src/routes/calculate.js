var express = require('express');
var router = express.Router();

// Require controller modules.
// var provider_controller = require('../controllers/providerController');
import calculate_controller from "../controllers/calculateController";

router.get('/', calculate_controller.calculate);

module.exports = router;