## DataLayer 数据层说明

> 这一层是在 Dao (源数据Model层) 之上做的一层封装，作用如下

1. 在这一层做一些元数据的处理, 如 json 数据的 decode 等
2. 在这一层做统一的 Cache 获取与设置. (缓存的更新放到 Model 层updated事件主动更新)
3. 在这一层做通用的公共数据处理的方法的封装.