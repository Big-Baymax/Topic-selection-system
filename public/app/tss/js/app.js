		//全局变量
		var root = 'http://topic.wangyouquan.cc/api/';
		var token = function() {
			return localStorage.getItem("token");
		}

		function createFragment(ul, index, refresh) {
			if(refresh) {
				ul.innerHTML = "";
			}
			data = getlist(index);
			//请求的页数不大于总页数
			if(index > data.data.page.page_count) {
				return true;
			} else {
				var fragment = create_topic(data.data.page.page_quantity, data.data);
				ul.appendChild(fragment);
				return false;
			}

		};

		var busying = false;
		//菜单切换
		function toggleMenu(menuWrapper, menu) {

			if(busying) {
				return;
			}
			busying = true;
			var menuWrapperClassList = menuWrapper.classList;
			if(menuWrapperClassList.contains('mui-active')) {
				//关闭
				downicon.className = 'mui-icon mui-icon-arrowdown';
				document.body.classList.remove('menu-open');
				menuWrapper.className = 'menu-wrapper fade-out-up animated';
				menu.className = 'menu bounce-out-up animated';
				setTimeout(function() {
					//backdrop.style.opacity = 0;
					menuWrapper.classList.add('hidden');
				}, 500);
			} else {
				document.body.classList.add('menu-open');
				menuWrapper.className = 'menu-wrapper fade-in-down animated mui-active';
				menu.className = 'menu bounce-in-down animated';
				//backdrop.style.opacity = 1;
			}
			setTimeout(function() {
				busying = false;
			}, 500);
		}

		//菜单点击设置选中
		mui('.menu').on('tap', 'li', function() {
			if(!this.classList.contains('selected')) {

				this.classList.add("selected");
				this.children[0].children[0].checked = true;

			} else {

				this.classList.remove("selected");
				this.children[0].children[0].checked = false;
			}
		});

		//菜单关闭
		mui('#class').on('tap', '#confirm', function() {
			createFragment(document.getElementById('topic'), 1, true)
			toggleMenu(classWrapper, classm)
		});

		mui('#class').on('tap', '#cancel', function() {

			toggleMenu(classWrapper, classm)
		});

		mui('#teacher').on('tap', '#confirm', function() {
			createFragment(document.getElementById('topic'), 1, true)
			toggleMenu(teacherWrapper, teacher)
		});

		mui('#teacher').on('tap', '#cancel', function() {

			toggleMenu(teacherWrapper, teacher)
		});

		mui('#sort').on('tap', '#confirm', function() {
			createFragment(document.getElementById('topic'), 1, true)
			toggleMenu(sortWrapper, sort)
		});
		mui('#sort').on('tap', '#cancel', function() {
			toggleMenu(sortWrapper, sort)
		});

		//拼接选题
		function selectjson(json, id) {
			for(var i in json) {
				if(json[i].id == id) {
					return json[i].name;
				}

			}
		}

		function create_topic(pagesize, data) {
			var fragment = document.createDocumentFragment();
			var div, son, sec, four;
			for(var i = 0; i < pagesize; i++) {
				div = document.createElement('div');
				div.setAttribute("data_id", data.topics[i].id)
				div.className = 'mui-card topic-card';
				son = document.createElement('div');
				son.className = 'mui-card-header';
				sec = document.createElement('p');
				sec.className = 'topic-title';
				sec.innerHTML = data.topics[i].name;
				son.appendChild(sec);
				sec = document.createElement('a');
				sec.className = 'mui-card-link teacher';
				sec.innerHTML = selectjson(data.teachers, data.topics[i].teacher_id);
				son.appendChild(sec);
				div.appendChild(son);
				son = document.createElement('div');
				son.className = 'mui-card-content';
				sec = document.createElement('div');
				sec.className = 'mui-card-content-inner';
				four = document.createElement('p');
				four.className = 'topic-class';
				four.innerHTML = '<i class="mui-icon mui-icon-paperclip topic-icon"></i>' + selectjson(data.categories, data.topics[i].category_id);
				sec.appendChild(four);
				four = document.createElement('p');
				four.className = 'topic-p';
				four.innerHTML = data.topics[i].description;
				sec.appendChild(four);
				son.appendChild(sec);
				div.appendChild(son);
				son = document.createElement('div');
				son.className = 'mui-card-footer';
				son.innerHTML = '<p class="topic-footer"><i class="mui-icon mui-icon-flag topic-icon"></i>发布时间：' + data.topics[i].created_at + '</p>';
				div.appendChild(son);
				fragment.appendChild(div);
			}
			return fragment;
		}
		//跳转
		function openpage(url, parm) {
			var open = mui.openWindow({
				url: url,
				extras: parm
			});
			return open;
		}

		//获取表单多选数组
		function getarry(obj) {
			var arry = [];
			for(var i = 0; i < obj.length; i++) {
				arry.push(obj[i].value);
			}
			return arry;
		}
		//获取查询数据
		function getform(index) {
			//获取多选数组
			$('#sort-form').serializeArray();
			var formval = {
				page: index,
				order_by: $("input[name='sort']:checked").val(),
				search_text: $("#search_text").val(),
				teacher: getarry($('#teacher-form').serializeArray()),
				topic_category: getarry($('#class-form').serializeArray()),
			}

			return formval;
		}
		//请求
		function getlist(index) {
			var getdata;
			mui.ajax(root + 'topics', {
				data: getform(index),
				headers: {
					'accessToken': token()
				},
				dataType: 'json', //服务器返回json格式数据
				type: 'get', //HTTP请求类型
				async: false, //同步执行
				timeout: 10000, //超时时间设置为10秒；
				complete: function() {},
				beforeSend: function() {},
				error: function(xhr, type, errorThrown) {
					console.log(JSON.stringify(xhr));
					console.log(xhr);
					//console.log(JSON.parse(xhr.response));
					//异常处理；
					try {
						var response = JSON.parse(xhr.response);
						mui.toast(response.message)
					} catch(e) {
						mui.toast('请检查网络是否通畅！');
					}

				},
				success: function(data) {
					getdata = data;
				}
			});
			return getdata;
		}
		//添加
		function addcheckbox(json, obj) {
			var fragment = document.createDocumentFragment();
			var ul = obj.children[0];
			ul.innerHTML='<div class="mui-col-xs-12" style="padding: 20px 0px;text-align: center;">'
							+'<button type="button" class=" mui-btn mui-btn-primary mui-col-xs-4" id="confirm">确定</button>&nbsp;'
							+'<button type="button" class=" mui-btn mui-btn-success mui-col-xs-4" id="cancel">取消</button>'
						+'</div>'; 
			var li, a;
			for(var i = 0; i < json.length; i++) {
				li = document.createElement('li');
				li.className = 'mui-col-xs-6 mui-table-view-cell';
				a = document.createElement('a');
				a.className = 'mui-navigate-right';
				a.innerHTML = json[i].name + '<input type="checkbox" value="' + json[i].id + '" name="menu[]" style="display: none;">';
				li.appendChild(a);
				fragment.appendChild(li);
			}
			ul.insertBefore(fragment, ul.firstChild);
		}

		//获取选题信息
		function getselect_topic(id) {
			var getdata;
			mui.ajax(root + 'topicsRecords', {
				data: {
					student_id: id,
				},
				headers: {
					'accessToken': token()
				},
				dataType: 'json', //服务器返回json格式数据
				type: 'get', //HTTP请求类型
				async: false, //同步执行
				timeout: 10000, //超时时间设置为10秒；
				complete: function() {},
				beforeSend: function() {},
				error: function(xhr, type, errorThrown) {
					//console.log(JSON.parse(xhr.response));
					//异常处理；
					try {
						var response = JSON.parse(xhr.response);
						mui.toast(response.message)
					} catch(e) {
						mui.toast('请检查网络是否通畅！');
					}

				},
				success: function(data) {
					getdata = data;
				}
			});
			return getdata;
		}

		//填充选题信息
		function showtopic(data) {

			if(data.length) {
				var html = '';
			} else {
				var html = '<p class="select-null">你还未选择选题！</p>';
			}
			for(var i = 0; i < data.length; i++) {
				if(data[i].status == '审核中') {
					html += '<div class="mui-card-header mui-card-media"><div class="mui-media-body" style="margin-left: 14px;font-size: 18px;"><a class="mui-card-link" id="status" data_id="' + data[i].topic_id + '">【' + (i + 1) + '】目前状态：' + data[i].status + '</a><p id="time">选择时间：' + data[i].created_at + '</p></div></div>' +
						'<div class="mui-card"><div class="mui-card-header"><h4 class="topic-title">' + data[i].topic + '</h4>' +
						'<a class="mui-card-link teacher">' + data[i].teacher + '</a>' + '</div>' +
						'<div class="mui-card-footer">' +
						'<button type="button" data-loading-text="提交中" class="mui-btn mui-btn-warning" id="select_cancel" data_id="' + data[i].id + '">取消重选</button>' +
						'<button type="button" data-loading-text="跳转中" class="mui-btn mui-btn-lan select-detail" data_id="' + data[i].topic_id + '">查看详情</button></div></div>';
				} else if(data[i].status == '取消重选') {
					html += '<div class="mui-card-header mui-card-media"><div class="mui-media-body" style="margin-left: 14px;font-size: 18px;"><a class="mui-card-link" id="status" data_id="' + data[i].topic_id + '">【' + (i + 1) + '】目前状态：' + data[i].status + '</a><p id="time">选择时间：' + data[i].created_at + '</p></div></div>' +
						'<div class="mui-card"><div class="mui-card-header"><h4 class="topic-title">' + data[i].topic + '</h4>' +
						'<a class="mui-card-link teacher">' + data[i].teacher + '</a></div>' +
						'<div class="mui-card-footer">' +
						'<a class="mui-card-link">已被取消</a>' +
						'<button type="button" data-loading-text="跳转中" class="mui-btn mui-btn-lan select-detail" data_id="' + data[i].topic_id + '">查看详情</button></div></div>';
				} else if(data[i].status == '重新选题申请通过') {
					html += '<div class="mui-card-header mui-card-media"><div class="mui-media-body" style="margin-left: 14px;font-size: 18px;"><a class="mui-card-link" id="status" data_id="' + data[i].topic_id + '">【' + (i + 1) + '】目前状态：' + data[i].status + '</a><p id="time">选择时间：' + data[i].created_at + '</p></div></div>' +
						'<div class="mui-card"><div class="mui-card-header"><h4 class="topic-title">' + data[i].topic + '</h4>' +
						'<a class="mui-card-link teacher">' + data[i].teacher + '</a></div>' +
						'<div class="mui-card-footer">' +
						'<a class="mui-card-link">现在可以重选</a>' +
						'<button type="button" data-loading-text="跳转中" class="mui-btn mui-btn-lan select-detail" data_id="' + data[i].topic_id + '">查看详情</button></div></div>';
				} else if(data[i].status == '申请重选中') {
					html += '<div class="mui-card-header mui-card-media"><div class="mui-media-body" style="margin-left: 14px;font-size: 18px;"><a class="mui-card-link" id="status" data_id="' + data[i].topic_id + '">【' + (i + 1) + '】目前状态：' + data[i].status + '</a><p id="time">选择时间：' + data[i].created_at + '</p></div></div>' +
						'<div class="mui-card"><div class="mui-card-header"><h4 class="topic-title">' + data[i].topic + '</h4>' +
						'<a class="mui-card-link teacher">' + data[i].teacher + '</a></div>' +
						'<div class="mui-card-footer">' +
						'<a class="mui-card-link">等待审核</a>' +
						'<button type="button" data-loading-text="跳转中" class="mui-btn mui-btn-lan select-detail" data_id="' + data[i].topic_id + '">查看详情</button></div></div>';
				} else {
					html += '<div class="mui-card-header mui-card-media"><div class="mui-media-body" style="margin-left: 14px;font-size: 18px;"><a class="mui-card-link" id="status" data_id="' + data[i].topic_id + '">【' + (i + 1) + '】目前状态：' + data[i].status + '</a><p id="time">选择时间：' + data[i].created_at + '</p></div></div>' +
						'<div class="mui-card"><div class="mui-card-header"><h4 class="topic-title">' + data[i].topic + '</h4>' +
						'<a class="mui-card-link teacher">' + data[i].teacher + '</a></div>' +
						'<div class="mui-card-footer">' +
						'<button type="button" data-loading-text="提交中" class="mui-btn mui-btn-warning" id="select_reelect" data_id="' + data[i].id + '">申请重选</button>' +
						'<button type="button" data-loading-text="跳转中" class="mui-btn mui-btn-lan select-detail" data_id="' + data[i].topic_id + '">查看详情</button></div></div>';
				}

			}
			document.getElementById('select-card').innerHTML = html;
		}