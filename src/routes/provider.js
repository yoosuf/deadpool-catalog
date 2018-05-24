var express = require('express');
var router = express.Router();

// Require controller modules.
// var provider_controller = require('../controllers/providerController');
import provider_controller from "../controllers/providerController";

router.get('/coinbase/gbp/buy', provider_controller.coinbase_buy_gbp);
router.get('/coinbase/gbp/sell', provider_controller.coinbase_sell_gbp);
router.get('/coinbase/cad/buy', provider_controller.coinbase_buy_cad);
router.get('/coinbase/cad/sell', provider_controller.coinbase_sell_cad);

/// PROVIDER ROUTES ///

// GET request for creating Provider. NOTE This must come before route for id (i.e. display provider).
router.get('/create', provider_controller.provider_create_get);

// POST request for creating Provider.
router.post('/create', provider_controller.provider_create_post);

// GET request to delete Provider.
router.get('/:id/delete', provider_controller.provider_delete_get);

// POST request to delete Provider.
router.post('/:id/delete', provider_controller.provider_delete_post);

// GET request to update Provider.
router.get('/:id/update', provider_controller.provider_update_get);

// POST request to update Provider.
router.post('/:id/update', provider_controller.provider_update_post);

// GET request for one Provider.
router.get('/:id', provider_controller.provider_detail);

// GET request for list of all Providers.
router.get('/providers', provider_controller.provider_list);

module.exports = router;