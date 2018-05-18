import express from "express";

const router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  res.json({
    message: "hell world",
    error: 200
  });
});

module.exports = router;
