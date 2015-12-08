* 2.0.0 (2015-xx-xx)
 - 升级组件到 2.0
 - 优化结构
 - 优化内部调度
 - 优化程序执行速度
 - 支持自定义模板扩展,函数,过滤器
 - 优化基础事件类 Event
 - 优化配置文件
 - 优化事件调度方式
 - 增加调度处理类
 - 集成PHPUnit, WebTestCase
 - 增加容器 singleton 方法,用于单例化对象
 - 增加路由请求事件转发功能 forward
 - 增加注释路由定义方式
 - 支持 Swoole Http Server 
 - 调整命令行, 修改Swole Http Server onWorkerStart 引导应用程序

**注意: 调用类请不要执行打印, 因为类中注入了Container, 整体容器如果进行打印的话,数据量会好大,配置稍微低一些的机器可能会比较慢**
