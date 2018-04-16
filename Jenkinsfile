pipeline {
  agent any
  stages {
    stage('unit test') {
      steps {
        echo 'run unit test'
      }
    }
    stage('deploy') {
      when {
        branch ('origin/pr/' + env.pullRequestId + '/from')
      }
      steps {
        echo 'deploying...'
      }
    }
    stage('GUI test') {
      when {
        branch ('origin/pr/' + env.pullRequestId + '/from')
      }
      steps {
        sh '''cd e2e/selenide
DISPLAY=:1 ./gradlew clean test'''
      }
    }
    stage('deploy to production') {
      steps {
        input '本番環境にデプロイします。よろしいですか？'
      }
    }
  }
}