作成者：LI YUNJUN
更新日付：2021/08/22

◆内容紹介
本コードはCloudFormationの使用方法を学習するために作った成果物です。
ネット上で掲載している「AWS基礎入門チュートリアル」（下記URL）をCloudFormation化することによって、
CloudFormationテンプレートの書き方とCodeDeployの使用方法等のインフラ自動化手法について身に着けましました。
本成果物ではCloudFormationを利用して以下のリソースの構築を自動化することができました。
    ●VPC
    ●サブネット
    ●インターネットゲートウェイ
    ●NATゲートウェイ
    ●EIP（NATゲートウェイ用）
    ●ルーティングテーブル/ルート
    ●セキュリティーグループ
    ●RDS（MySQL、DBSubnetGroup）
    ●ALB（Listener、TargetGroup）
    ●CodeDeploy（ポリシー、ロール、）
    ●AutoScaling（ASGグループ、LaunchTemplate）
    ●CodeDeploy（アプリケーション、DeploymentGroup）
    ●Route53（ホストゾーン、レコード）
    ●EC2内のミドルウェアの自動インストール

アプリ基本構成
                           IGW
                            |
--Public Subnet-------------|-------------------------
|　　　    　              ALB                        |
---------------------------/ \------------------------
                          /   \
--Public Subnet----------/     \----------------------
|   --Auto Scaling Group/-------\----------------    |
|   |                  /         \              |    |
|   |      EC2(AP server 1)   EC2(AP server 2)  |    |
|   |                  \         /              |    |
|   --------------------\-------/----------------    |
-------------------------\     /----------------------
                          \   /
--Private Subnet------------\ /-----------------------
|　　　     　           RDS(MySQL)                   |
------------------------------------------------------

参考資料抜粋：
    AWS基礎入門チュートリアル：https://cloud-textbook.com/114/
    CloudFormation：https://d1.awsstatic.com/webinars/jp/pdf/services/20200826_AWS-BlackBelt_AWS-CloudFormation.pdf
                    https://dev.classmethod.jp/articles/list-of-cloudformation-parameters-by-data-type/
                    https://www.ctc-g.co.jp/solutions/cloud/column/article/07.html
                    https://docs.aws.amazon.com/ja_jp/AWSCloudFormation/latest/UserGuide/parameters-section-structure.html
                    https://docs.aws.amazon.com/ja_jp/AWSCloudFormation/latest/UserGuide/intrinsic-function-reference.html
                    https://docs.aws.amazon.com/AWSCloudFormation/latest/UserGuide/aws-template-resource-type-ref.html
    Former 2チュートリアル：https://dev.classmethod.jp/articles/former2/
                        　https://qiita.com/sakai00kou/items/9bdd23d18d38725d8ec8
    Former 2：https://former2.com/#
    CodeDeployチュートリアル：https://docs.aws.amazon.com/ja_jp/codedeploy/latest/userguide/tutorials-auto-scaling-group.html
    VS Code拡張機能(CFnテンプレート編集に役立つ)：https://marketplace.visualstudio.com/items?itemName=kddejong.vscode-cfn-lint
                                            　　https://marketplace.visualstudio.com/items?itemName=eastman.vscode-cfn-nag



◆コード使用説明
続きの内容はCloudFormationテンプレートとアプリ素材の使用説明となります。

mini使用説明：
    1.AWSマネジメントコンソールからCloudFormationスタックを作成します。

    CloudFormationマネジメントコンソールURL：
    https://us-west-2.console.aws.amazon.com/cloudformation/home?region=us-west-2#/

    CloudFormationテンプレート：asg-ap1.yaml
        スタックの名前：任意
        パラメータ：
            AZ1：任意のアベイラビリティゾーンを選択
            AZ2：AZ1と違うAZを選択
            EC2key：既存のEC2キーペアを選択（既存のキーがなければスタック作成する前に作成して置き、キーへのアクセス権を持つを確保すること。）
            ProjectName：任意の名前。この名前は作成されてリソースの名前の一部として使用されます。
        出力：
            AppDeploy：ユーザーにこのドキュメントの次の指示に従ってアプリデプロイとDBセットアップをさせるための指示である。
                        CodeDeployの初回デプロイは必ず手動で行う制限があるため、CFnで自動化できない部分となります。
                        またDB内部のセットアップはCFnの設定対象外となるため、AppサーバーからSQLスクリプトを実行して設定します。
                        上記手順はWebsiteURLにアクセスする前に設定して置く必要があります。
            WebsiteURL：本アプリのURL

    ２．アプリをデプロイする。

    下記URLから「${ProjectName}.-application」の詳細設計画面に入って、
    アプリケーションのデプロイを行ってください。

    CodeDeployマネジメントコンソールURL：
    https://us-west-2.console.aws.amazon.com/codesuite/codedeploy/applications

        マネジメントコンソール操作手順：
        ①　CFnが作成したアプリケーション名「${ProjectName}-application」をクリック
        ②「デプロイ」タブをクリック
        ③「デプロイの作成」ボタンをクリック
        ④　デプロイ設定
            ・デプロイグループに「${ProjectName}-DeploymentGroup」を選択
            ・リビジョンタイプに「アプリケーションは Amazon S3 に格納されています」を選択
            ・添付ファイル内の「minitwitter/minitwitter.zip」を自分のS3バケットにアップロードし、
            　「リビジョンの場所」にminitwitter.zipのS3 URLを貼り付け
            ・「デプロイを作成」ボタンをクリック
            デプロイが完了するまで10分ほど掛かる事があります。
            デプロイメントの詳細画面で「デプロイのライフサイクルイベント」→「View events」で進捗が見れます。

    ３．デプロイ完了後Appサーバーにssh接続し、「/home/ec2-user/scripts/DB-setup.sh」を実行してください。
        DBの初期設定は自動的に行われます。
        DB-setup.shの実行権限は自動的に設定されているので、スプリプとをそのまま実行してよいです。
        Appサーバーは「${ProjectName}-AP」と言う名前になります。
        EC2管理画面からAppサーバーのパブリックIPでアクセスしてください。
        Appサーバーは複数存在していますが、SQLスクリプトはアプリ資材と一緒に各サーバーに配布されているので、
        任意のAppサーバーに接続してDBの初期設定を一度だけ行って良いです。



◆本コードの欠点（残っている改善点）
1. DBの初期設定にスクリプトを用意したが、手動で実行しなければならりませんので、
　　更なる自動化にするには、DBの初期設定をアプリのPHPコードに組み込んて、
　　初回実行時DBを設定するような構成にしたが良いのではないかと思いました。
　　（自分はPHPが書けないため、割愛しました。）
２．DB接続データとパスワードはCFnテンプレート、スクリプトに書き込んでいますが、
　　Secrete Manager等に移管して、そこから参照する形にした方がセキュリティーのベストプラクティスに沿ったやり方となります。
３．CodeDeployの初回デプロイはAWSの制限により手動で実行しなければならないこととなっていますが、
　　AWS CLIでデプロイを行えば、デプロイコマンドをスクリプト化してより安定したデプロイができるかと思いました。
４．APサーバーへはDB初期設定をする時にsshで直接接続する必要があるため、Publicサブネット内に置いてありますが、
　　セキュリティーを向上するため、APサーバーへはプライベートサブネットに配置し、
　　ALBのみを公開する構成にした方が良いではないかと思います。
　　また、APサーバーへ接続、APサーバーからインターネットにアクセスする（ＭＷインストール）必要があると時、
　　パブリックサブネットに踏み台サーバーとNATゲートウェイを用意した方がよいではないかと思います。