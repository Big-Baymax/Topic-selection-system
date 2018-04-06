# Topic-selection-system
学生选题系统

## 项目环境
1. 运行环境   
    - appache + mysql + php   
2. 入口文件   
   
3. 使用框架 
    - Laravel 
## 接口使用说明
   ### 信息管理分页参数说明
   - 请求
   -------------
   pageNumber：第几页
   pageSize：每页几条记录；
   sortName：根据这个字段排序；
   sortOrder：按照正序或者倒序；
   searchText：搜索关键字；
   
   - 返回
   total: 总共有几条数据
   data: 数据记录的详细参数
   
## 项目配置  
- 设置配置信息:   
```
setConfig({
    singly: num, // 单间数量
    double: num, // 标间数量
    classroom: '多功能厅,第一教室,第二教室,第四教室,第五教室,第六教室,第七教室', //教室
    meeting: '第4讨论室,第5讨论室,第6讨论室,第7讨论室,第8讨论室,第9讨论室,第10讨论室,第11讨论室,第12讨论室,第13讨论室,第14讨论室,第15讨论室,第16讨论室' //讨论室
}); 
``` 
- 获取配置内容  
getConfig();

- 获取单位列表
** 在设置教师/讨论室安排前使用该方法获取单位名称及其对应的id **
getUnitAndid(start_time, end_time);

- 设置单位入住计划
setPlanHousing();

- 获取剩余可安排信息
getInfoPlan(start_time, end_time); // 当天剩余房间数 singly 单间 double 标间 教室待定
** 缺省的开始时间为当前时间, 缺省的结束时间为10年 **

- 设置单位的教室安排
```javascript   
setPlanClassroom(
    belong_id: //所属单位id
    room: // 使用的教室
    time_flag: // 标记上午/下午/晚上
    start_time: // 开始时间
    end_time: // 结束时间
)
updatePlanClassroom(id, room, time_flag, start_time, end_time); // 更新教室安排
deletePlanClassroom(id); // 删除教室安排
```

- 设置单位的讨论室安排
```javascript   
setPlanMeeting(
    belong_id, //所属单位id
    content, // 使用的讨论室
    time_flag, // 标记上午/下午/晚上
    time, // 使用时间
)
updatePlanMeeting(id, content, time_flag, time); //更新讨论室安排
deletePlanMeeting(id); // 删除讨论室安排
```

- 数据库文件备份相关
```javascript
backup(); // 备份文件，文件按时间戳进行
getBackup(); //获取备份文件列表
restore(fileName); // 恢复备份，需要传入一个文件名
```

- 导出表格相关
```javascript
exportToExcel(filenameAndPath, startTime, endTime); // 导出excel表格
exportToExcelOfAll(filenameAndPath); // 导出excel表格  全部内容
```
     
### 房间与教室相关
- 获取配置   
    所有的安排: `getAll('table'); // table = housing | classroom`
- 设置安排
    ```javascript
    setPlan('table', obj); 
    // table = housing | classroom 
    // obj = {unit: '单位名称', total: num, singly: num, double: num, start_time: 'xxxx-xx-xx', end_time: 'xxxx-xx-xx'}
    // obj = {unit: '单位名称', type: '教室类型', start_time: 'xxxx-xx-xx', end_time: 'xxxx-xx-xx'
    // return {error: false | true; msg: result|errorMsg }
    ```
 

    


## 数据库配置

### plan_housing 住宿安排

|     名称    |   类型   |     描述    |
|:----------:|:-------:|:-----------:|
| id         | INTEGER | 住宿安排id    |
| unit       | TEXT    | 入住单位名称  |
| total      | INTEGER | 入住总人数    |
| singly     | INTEGER | 使用单间数量   |
| double     | INTEGER | 使用标间数量   |
| start_time | TEXT    | 入住开始时间   |
| end_time   | TEXT    | 入住结束时间   |

### plan_classroom  教室安排

|     名称    |   类型   |     描述    |
|:----------:|:-------:|:-----------:|
| id         | INTEGER | 教室安排id    |
| belong_id  | INTEGER | 入住单位id    |
| room       | TEXT    | 使用教室/单选  |
| start_time | TEXT    | 时间         |
| end_time   | TEXT    | 时间         |
| time_flag  | INTEGER | 上午/下午/晚上 |

### plan_meeting 讨论室安排

|     名称    |   类型   |     描述    |
|:----------:|:-------:|:-----------:|
| id         | INTEGER | 讨论室安排id   |
| belong_id  | INTEGER | 入住单位id     |
| content    | TEXT    | 使用讨论室/多选 |
| start_time | TEXT    | 入住时间       |
| end_time   | TEXT    | 入住时间       |
| time_flag  | INTEGER | 上午/下午/晚上  |