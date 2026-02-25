#!/bin/bash

cd $(dirname $0)

while getopts "t:c:b:n:" OPT
do
    case $OPT in
        t) ZA_TARGET=${OPTARG} ;;
        c) ZA_CONTEXT=${OPTARG} ;;
        b) ZA_BEFORE_SCRIPT=${OPTARG} ;;
        n) ZA_THREAD_PER_HOST=${OPTARG} ;;
    esac
done

ZA_THREAD_PER_HOST=${ZA_THREAD_PER_HOST:-10}

if [[ -z "${ZA_CONTEXT}" ]]; then
    if [[ ${ZA_TARGET} =~ 'admin' ]]; then
        ZA_CONTEXT=admin
        ZA_USER=admin
        ZA_FORCE_ADMIN_CONFIG="
  - type: script
    parameters:
      action: add
      type: standalone
      name: forceuser
      file: /zap/wrk/scripts/forceuser.groovy

  - type: script
    parameters:
      action: run
      type: standalone
      name: forceuser
"
    else
        ZA_CONTEXT=default
    fi
fi

ZA_BEFORE_SCRIPT=$(echo ${ZA_BEFORE_SCRIPT} | sed 's/ //g')

echo "
CONTEXT: ${ZA_CONTEXT}
USER: ${ZA_USER}
THREAD_PER_HOST: ${ZA_THREAD_PER_HOST}
TARGET: ${ZA_TARGET}
BEFORE_SCRIPT: ${ZA_BEFORE_SCRIPT}
"

if [[ -n ${ZA_BEFORE_SCRIPT} ]]; then
    ZA_BEFORE_SCRIPT_CONFIG="
  - type: script
    parameters:
      action: add
      type: sequence
      name: before_script
      file: /zap/wrk/scripts/${ZA_BEFORE_SCRIPT}
  - type: script
    parameters:
      action: run
      type: sequence
      name: before_script"
fi

TEMPLATE=$(sed 's/"/\\"/g' automation/template.yml)
eval "echo \"${TEMPLATE}\"" > automation/${ZA_TARGET}.yml