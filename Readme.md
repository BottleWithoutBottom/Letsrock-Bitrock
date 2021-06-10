**Подключение к проекту**

Для того, чтобы битрок работал на проекте, нужно :
1) Создать конфигурационный файл в какой-нибудь папке проекта (/local/.env);
2) Установить настройки переменных окружения (их описания смотри в файле .env.example);
3) В init.php подгрузить файлы из composer-autoload;

(Далее следует работа с ядром: Bitrock\LetsCore в файле init.php)

4) Вызвать статический метод LetsCore::parseConfiguration($envPath), где $envPath - путь до папки с файлом .env, созданного ранее (ИМЕННО ПАПКИ, А НЕ ФАЙЛА);
5) Вызвать метод LetsCore::execute(), чтобы ядро подключило сервисы, которые идут вместе с ним (автоматическая генерация моделей при создании и изменении инофблока, например).

**Использование собственных классов**

Для того, чтобы использовать собственные классы в связке с Bitrock (например, контроллеры, для обработки роутов, или классы, которые будут погружаться в конфиге DI), нужно добавить их в composer-autoload следующим образом

`
"autoload": {
    "psr-4": {
        "*Неймспейс*\\": "директория"
    }
}
`

**Запуск роутера**
1. Для запуска роутера нужно установить BOOTSTRAP_MODE в .env в значение, отличное от пустоты [1];
2. Указать путь до файла, на котором будет обрабатываться роутер, относительно корня сайта, BOOTSTRAP_PATH в .env;
   
   2.1. Не забыть создать файл по этому пути (bootstrap.php);
   
3. Добавить виртуальный путь, на который будут лететь все ajax-запросы в BOOTSTRAP_URL в .env (/ajax-virtual/, по умолчанию);
4. Теперь нужно настроить редирект с BOOTSTRAP_URL на BOOTSTRAP_PATH в настройках сервера:
   
   4.1 пример для Apache: 
    Вместо BOOTSTRAP_URL и BOOTSTRAP_PATH подставить настоящие значения, указанные в .env
   
   **!!! Данный блок ставить раньше всех остальных редиректов !!!**

`
   <IfModule mod_rewrite.c>
   RewriteEngine on
   RewriteBase /
   RewriteRule ^(.*)BOOTSTRAP_URL(.*)$ BOOTSTRAP_PATH?$1 [QSA,L]
   </IfModule>
`

(Далее речь пойдет об абстрактном классе Bitrock\Router\Router)
5. Подключить в BOOTSTRAP_PATH файле загрузку файлов из композера, и распарсить переменные окружения из .env:
`
use Bitrock\LetsCore;
require($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');
LetsCore::parseConfiguration($_SERVER['DOCUMENT_ROOT'] . '/local/');
`

    5.1 В данном файле нужно вызвать обработку виртуальных путей роутером (по умолчанию используется Bitrock\Router\FastRouter), но использовать можно любой, отнаследовавшись от Bitrock\Router\Router. Обработку можно запустить при помощи инициализации объекта выбранного роутера, и вызова у него метода handle():
    `
       $router = Bitrcok\Router\FastRouter::getInstance();
       // Роуты
       $router->handle();
    `
   

6. Добавление обрабатываемых роутов (для FastRoute):
До запуска метода handle() нужно добавить роуты следующим образом:
   
`$router->addRoute([
    $router::METHOD => [*Массив доступных методов('GET', 'POST')*],
    $router::URL => *урл роута(с учетом BOOTSTRAP_URL)*
    $router::HANDLER => [*Название класса контроллера*, *название метода, который будет вызван*]
])`

Пример:

`
$bootstrapPath = Bitrock\LetsCore::getEnv(LetsCore::BOOTSTRAP_URL);
$router->addRoute([
    $router::METHOD => ['GET'],
    $router::URL => $bootstrapPath . 'test-route/'
    $router::HANDLER => [*Bitrock\Controllers\TestController*, 'testMethod']
])`

т.о., при попытке кинуть GET аякс запрос на путь /ajax-virtual/test-route/ будет вызван метод testMethod у класса TestController

**Автоматическая генерация моделей для инфоблоков Bitrix**
