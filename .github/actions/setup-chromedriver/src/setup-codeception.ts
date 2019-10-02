import * as core from "@actions/core";
import * as exec from "@actions/exec";
import * as path from "path";

async function run() {
  try {
    console.log(`##setup codeception`);
    await exec.exec(path.join(__dirname, "setup-codeception.sh"));
  } catch (error) {
    core.setFailed(error.message);
  }
}

run();
