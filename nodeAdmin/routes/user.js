"use strict";

import express from "express";
import User from "../controller/userController";
const router = express.Router();

router.post("/login", User.login);

export default router;
