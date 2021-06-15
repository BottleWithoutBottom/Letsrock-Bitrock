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

```
#!php
use Bitrock\LetsCore;
require($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');
LetsCore::parseConfiguration($_SERVER['DOCUMENT_ROOT'] . '/local/');
```

5.1 В данном файле нужно вызвать обработку виртуальных путей роутером (по умолчанию используется Bitrock\Router\FastRouter), но использовать можно любой, отнаследовавшись от Bitrock\Router\Router. Обработку можно запустить при помощи инициализации объекта выбранного роутера, и вызова у него метода handle():

```
#!php
   $router = Bitrcok\Router\FastRouter::getInstance();
   // Роуты
   $router->handle();
```

6. Добавление обрабатываемых роутов (для FastRoute):
До запуска метода handle() нужно добавить роуты следующим образом:

```
#!php
$router->addRoute([
    $router::METHOD => [*Массив доступных методов('GET', 'POST')*],
    $router::URL => *урл роута(с учетом BOOTSTRAP_URL)*
    $router::HANDLER => [*Название класса контроллера*, *название метода, который будет вызван*]
])
```

Пример:

```
#!php
$bootstrapPath = Bitrock\LetsCore::getEnv(LetsCore::BOOTSTRAP_URL);
$router->addRoute([
    $router::METHOD => ['GET'],
    $router::URL => $bootstrapPath . 'test-route/'
    $router::HANDLER => [*Bitrock\Controllers\TestController*, 'testMethod']
])
```

т.о., при попытке кинуть GET аякс запрос на путь `/ajax-virtual/test-route/` будет вызван метод testMethod у класса `TestController`

**Автоматическая генерация моделей для инфоблоков Bitrix**

**Logger**

Логгер в пакете реализует psr и использует Monolog под капотом.

Использование:

```
#!php
use Bitrock\Utils\Logger\Logger;
$logger = Logger::getInstance();
$logger->info(`User with login ${$login} successfully logged in`);
```

По умолчанию, логи будут записываться в файл, указанный в LOG_PATH в .env файле.

Но есть возможность изменить файл для записи при помощи метода `setLogPath()`:

```
#!php
use Bitrock\Utils\Logger\Logger;
$logger = Logger::getInstance();
$logger->setLogPath(*Путь до лог-файла*);
$logger->info(`User with login ${$login} successfully logged in`);
```

**Resizer**
Класс ресайзер используется для того, чтобы сократить количество кода при ресайзе картинок.

Достигается это посредством того, что настройки каждого ресайза помещаются в отдельный файл, и не загромождают код, в котором используется ресайз.

Использование:

1) Необходимо в .env указать путь до файла, который возвращает массив с настройками ресайзов (RESIZES_STORAGE_PATH);
   
2) Создать этот файл с подобным содержимым:

```
#!php
return [
    'FIRST_RESIZE' => [
        'WIDTH' => 350,
        'HEIGHT' => 350,
        'BX_RESIZE' => BX_RESIZE_IMAGE_PROPORTIONAL
    ]
]
```

Здесь `'FIRST_RESIZE' - ключ, по которому, в дальнейшем, можно использовать ресайз, а внутри него настройки конкретного ресайза`;

3) Использование ресайзера в коде:

```
#!php
use Bitrock\Utils\Resizer;

$resizer = Resizer::getInstance();

//$resizedPicture = $resizer->getResizeImageArray(*массив файла для ресайза*, *Ключ ресайза из конфига*);
$resizedPicture = $resizer->getResizeImageArray($resizeArray, 'FIRST_RESIZE');
```

Также, доступен метод `getResizeImageArrayById($id, *Ключ ресайза из конфига*)`, который позволяет получить ресайз картинки по ID файла, а не массива с данными файла

**BitrockController**

Класс `Bitrock\Controllers\Controller` структурирует и облегчает работу с AJAX-запросами, если используется какой-либо роутер.

Для того, чтобы использовать возможности контроллера, нужно создать свой контроллер и отнаследовать его от `Bitrock\Controllers\Controller`.

Т.о. становятся доступны два метода: `json($data = [], $message = '', $status = true, $httpStatus)` и `render($viewName, $params = [], $httpStatus)`;

Метод `json`:

Метод json возвращает с сервера данные в виде json строки и принимает следующие параметры:

1) `$data` - массив с возвращаемыми данными;
2) `$message` - сообщение с ответом от сервера;
3) `$status` - статус выполнения скрипта (может быть важно для выполнения действий джаваскриптом);
4) `$httpStatus (по умолчанию 200)` - статус ответа от сервера.

метод render возвращает с сервера кусок верстки из вьюшки (обычный php-файл) и принимает следующие параметры:

1) `$viewName` - название файла, верстка которого будет возвращена;
2) `$params` - массив с параметрами, которые будут завернуты в переменные и могут быть использованы внутри файла вьюшки, где ключ параметра превратится в название переменной, а значение параметра - в значение этой переменной;
3) `$httpStatus (по умолчанию 200)` - статус ответа от сервера.
