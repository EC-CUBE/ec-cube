pipeline {
  agent any
  stages {
    stage('unit test') {
      steps {
        echo 'run unit test'
      }
    }
    stage('deploy') {
      steps {
        echo 'deploying...'
      }
    }
    stage('GUI test') {
      steps {
        sh '''cd e2e/selenide
DISPLAY=:1 ./gradlew clean test'''
      }
    }
    stage('deploy to production') {
      steps {
        input 'Deploying to production. Are you sure?'
      }
    }
  }
}