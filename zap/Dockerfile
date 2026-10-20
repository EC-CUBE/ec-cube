FROM ghcr.io/zaproxy/zaproxy:stable

USER root
RUN apt-get update \
  && apt-get install --no-install-recommends -y \
  fonts-noto-cjk

USER zap
