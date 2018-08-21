
var stopBubble = function(a) {
    if (a && a.stopPropagation) {
        a.stopPropagation();
    }
    if (window.event) {
        window.event.cancelBubble = true;
        return false;
    }
    return false;
};
var HttpService = function() {
    this.MAX_VALUE = 100000;
    var c = {
        post: "POST",
        get: "GET"
    };
    var a = function(i, g, j, d, f) {
        $(document.activeElement).blur();
        var e = JSON.parse(sessionStorage.getItem("userInfo"));
        var h = null;
        if (e) {
            h = e.token;
        }
        $.ajax({
            headers: {
                "content-type": "application/x-www-form-urlencoded"
            },
            type: i,
           // url: config.apiUrl + g,
            url:  g,
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            data: j,
            success: function(k) {
                if (typeof(d) == "function") {
                    return d(k);
                } else {
                    console.log("the method is no a function!");
                }
            },
            error: function(k) {
                if (typeof(f) == "function") {
                    f(k);
                } else {
                    console.log("the method is no a function!");
                }
            }
        });
    };
    var b = function(g, f, h, d, e) {
        $.ajax({
            url: config.apiUrl + f,
            type: g,
            data: h,
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            success: function(i) {
                if (typeof(d) == "function") {
                    return d(i);
                } else {
                    console.log("the method is no a function!");
                }
            },
            error: function(i) {
                if (typeof(e) == "function") {
                    e(i);
                } else {
                    console.log("the method is no a function!");
                }
            }
        });
    };
    this.upload = function(f, g, d, e) {
        return b(c.post, f, g, d, e);
    };
    this.get = function(f, g, d, e) {
        return a(c.get, f, g, d, e);
    };
    this.post = function(f, g, d, e) {
        return a(c.post, f, g, d, e);
    };
};
var common = {
    ajax: new HttpService(),
    userInfo: sessionStorage.getItem("userInfo") ? JSON.parse(sessionStorage.getItem("userInfo")) : "",
    imgExport: function(f, b) {
        $(document.activeElement).blur();
        function a() {
            var i = navigator.userAgent;
            var h = i.indexOf("Opera") > -1;
            if (h) {
                return "Opera";
            }
            if (i.indexOf("Firefox") > -1) {
                return "FF";
            }
            if (i.indexOf("Chrome") > -1) {
                return "Chrome";
            }
            if (i.indexOf("Safari") > -1) {
                return "Safari";
            }
            if (i.indexOf("compatible") > -1 && i.indexOf("MSIE") > -1 && !h) {
                return "IE";
            }
            if (i.indexOf("Trident") > -1) {
                return "Edge";
            }
        }
        function g(j, i) {
            var h = d(i);
            window.navigator.msSaveBlob(h, j);
        }
        function d(j) {
            var h = j.split(","),
                l = h[0].match(/:(.*?);/)[1],
                i = atob(h[1]),
                m = i.length,
                k = new Uint8Array(m);
            while (m--) {
                k[m] = i.charCodeAt(m);
            }
            return new Blob([k], {
                type: l
            });
        }
        if (a() === "IE" || a() === "Edge") {
            g(b, f);
        } else {
            var c = document.createElement("a");
            c.href = f;
            c.download = b;
            var e = document.createEvent("MouseEvents");
            e.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
            c.dispatchEvent(e);
        }
        common.loadBox.hide();
    },
    getDate: function() {
        return {
            formater: function(b) {
                var c = b.getFullYear(),
                    d = this.twoDigit(b.getMonth() + 1),
                    a = this.twoDigit(b.getDate());
                return c + "-" + d + "-" + a;
            },
            formaterDT: function(c) {
                var f = c.getFullYear(),
                    g = this.twoDigit(c.getMonth() + 1),
                    b = this.twoDigit(c.getDate()),
                    e = this.twoDigit(c.getHours()),
                    a = this.twoDigit(c.getMinutes()),
                    d = this.twoDigit(c.getSeconds());
                return f + "-" + g + "-" + b + " " + e + ":" + a + ":" + d;
            },
            twoDigit: function(a) {
                if (a >= 10) {
                    return a;
                }
                if (a < 10) {
                    return ("0" + a);
                }
                return a;
            },
            previousDay: function(a, c) {
                var b = new Date(a.getTime() - c * 24 * 60 * 60 * 1000);
                return this.formater(b);
            },
            nextDay: function(a, c) {
                var b = new Date(a.getTime() + c * 24 * 60 * 60 * 1000);
                return this.formater(b);
            },
        };
    },
    convertCanvasToImage: function(b, a) {
        var c = new Image();
        var d = common.getDate().formaterDT(new Date());
        c = b.toDataURL("image/png").replace("image/png", "image/octet-stream");
        common.imgExport(c, a + d.substring(0, 10) + "_" + d.substring(11) + ".png");
    },
    getParamFromUrl: function(a) {
        var b = new RegExp("(^|&)" + a + "=([^&]*)(&|$)");
        var c = window.location.search.substr(1).match(b);
        if (c != null) {
            return unescape(c[2]);
        }
        return null;
    },
    getUrlArgObject: function() {
        var b = {},
            f = location.search.substring(1),
            e = f.split("&");
        for (var c = 0; c < e.length; c++) {
            var g = e[c].indexOf("=");
            if (g == -1) {
                continue;
            }
            var a = e[c].substring(0, g);
            var d = e[c].substring(g + 1);
            b[a] = decodeURI(d);
        }
        return b;
    },
    loginOut: function() {
        var a = this.getUrlArgObject(),
            b = a ? a.p: "";
        sessionStorage.clear();
        if (b == "h") {
            location.href = "home.html";
        } else {
            location.href = "login.html";
        }
    },
    initOptions: function(f, c, e) {
        e = e || "";
        var b = f.attr("add-null") == 1 ? true: false;
        if (b) {
            var d = f.attr("add-null-text");
            d = d || "--请选择--";
            f.append('<option value="" selected>' + d + "</option>");
        }
        for (var a in c) {
            if ((e + "") == a) {
                f.append('<option value="' + a + '" selected>' + c[a] + "</option>");
            } else {
                f.append('<option value="' + a + '">' + c[a] + "</option>");
            }
        }
    },
    selectSingleAera: function(a) {
        top.layer.open({
            type: 1,
            title: "请选择区域",
            area: ["610px", ""],
            btn: ["确定", "取消"],
            btnAlign: "c",
            content: '<div class="tree-container"><div class="tree-box" style="width: 600px;"><div id="tree" style="height: 340px;"></div></div></div>',
            success: function(c, e) {
                var f = $(c).find("#tree");
                var b = function(h) {
                    h.text = h.name;
                    if (h.isSite == 1) {
                        h.icon = "glyphicon glyphicon-link";
                        h.selectable = false;
                    } else {
                        h.selectable = true;
                        h.icon = "glyphicon glyphicon-pushpin";
                    }
                    h.nodes = h.children && h.children.length > 0 ? h.children: null;
                    if (h.nodes !== null) {
                        for (var g = 0; g < h.children.length; g++) {
                            var j = h.children[g];
                            b(j);
                        }
                    }
                };
                var d = function(h, i) {
                    if (!i.nodes) {
                        var g = "/documentaryarea/back/sub/" + i._id;
                        common.ajax.get(g, null,
                            function(j) {
                                if (j.code == 1001) {
                                    $.each(j.result,
                                        function(l, m) {
                                            b(m);
                                            var k = {
                                                node: m,
                                                silent: false
                                            };
                                            f.treeview("addNode", [i.nodeId, k]);
                                        });
                                    f.treeview("expandNode", [i.nodeId, {
                                        levels: 2,
                                        silent: true
                                    }]);
                                }
                            });
                    }
                };
                common.ajax.get("documentaryarea/back/list", {},
                    function(g) {
                        if (g.code == "1001") {
                            b(g.result);
                            f.treeview({
                                color: "#676a6c",
                                multiSelect: false,
                                data: [g.result],
                                showBorder: false,
                            });
                        } else {
                            common.nomalPrompt("未查询到区域信息");
                            return;
                        }
                    });
            },
            yes: function(c, b) {
                var d = $(b).find("#tree").treeview("getSelected");
                if (d.length == 0) {
                    common.nomalPrompt("您还未选择区域");
                    return;
                }
                a(d[0]);
                top.layer.close(c);
            }
        });
    },
    selectMultiAreas: function(a) {
        top.layer.open({
            type: 1,
            title: "请选择区域",
            area: ["848px"],
            btn: ["确定", "取消"],
            btnAlign: "c",
            content: '<div class="tree-container"><div class="tree-box" style="width: 520px"><div class="tree-title">区域树结构</div><div id="tree"></div></div><div class="tree-box" style="width: 300px"><div class="tree-title">选择区域<span class="removeAll"><i class="fa fa-trash mR5"></i>清除</span></div><div id="tree-persons"></div></div></div>',
            success: function(c, d) {
                $(c).find("span.removeAll").click(function() {
                    $(c).find("#tree-persons").find(".op").each(function(e, f) {
                        $(this).click();
                    });
                });
                $(c).find("#tree-persons").on("click", ".op",
                    function() {
                        var e = $(this).parents(".msgModel"),
                            f = e.data("info");
                        e.remove();
                        $(c).find("#tree").treeview("unselectNode", [f.nodeId, {
                            silent: true
                        }]);
                    });
                var b = function(f) {
                    f.text = f.name;
                    if (f.isSite == 1) {
                        f.icon = "glyphicon glyphicon-link";
                        f.selectable = false;
                    } else {
                        f.icon = "glyphicon glyphicon-pushpin";
                    }
                    f.nodes = f.children && f.children.length > 0 ? f.children: null;
                    if (f.nodes !== null) {
                        for (var e = 0; e < f.children.length; e++) {
                            var g = f.children[e];
                            b(g);
                        }
                    }
                };
                common.ajax.get("documentaryarea/back/list", {},
                    function(e) {
                        if (e.code == "1001") {
                            b(e.result);
                            $(c).find("#tree").treeview({
                                color: "#676a6c",
                                multiSelect: true,
                                data: [e.result],
                                showBorder: false,
                                onNodeSelected: function(f, g) {
                                    $(c).find("#tree-persons").append('<div class="msgModel" id="area_' + g._id + '"><span class="op"><i class="fa fa-trash"></i></span><h4 class="mB5">' + g.name + '</h4><div class="date">' + g.address + "</div></div>");
                                    $(c).find("#area_" + g._id).data("info", g);
                                },
                                onNodeUnselected: function(f, g) {
                                    $(c).find("#area_" + g._id).find(".op").click();
                                }
                            });
                        } else {
                            layer.msg(e.result);
                        }
                    });
            },
            yes: function(c, b) {
                var d = [];
                $(b).find(".msgModel").each(function(e, f) {
                    var g = $(f).data("info");
                    d.push(g);
                });
                a(d);
                top.layer.close(c);
            }
        });
    },
    selectSingleOrg: function(b, a) {
        top.layer.open({
            type: 1,
            title: "请选择组织",
            area: ["414px"],
            btn: ["确定", "取消"],
            btnAlign: "c",
            content: '<div class="tree-container"><div class="tree-box" style="width: 400px;"><div id="tree" style="height: 340px;"></div></div></div>',
            success: function(d, e) {
                var c = function(g) {
                    g.text = g.name;
                    g.nodes = g.children && g.children.length > 0 ? g.children: null;
                    if (g.nodes !== null) {
                        for (var f = 0; f < g.children.length; f++) {
                            var h = g.children[f];
                            c(h);
                        }
                    }
                };
                common.ajax.get(departmenturl, {},
                    function(f) {
                        if (f.status == 1) {
                            f.result.selectable = false;
                            c({
                                name: "全部",
                                children: f.result
                            });
                            top.$("#tree").treeview({
                                color: "#676a6c",
                                multiSelect: false,
                                data: f.result,
                                showBorder: false,
                            });
                        } else {
                            top.layer.msg(f.result);
                        }
                    });
            },
            yes: function(e, d) {
                var c = function(h) {
                    var g = true;
                    while (g) {
                        var i = top.$("#tree").treeview("getParent", h);
                        var j = top.$("#tree").treeview("getParent", i);
                        if (typeof(j.parent) !== "function") {
                            h = i;
                        } else {
                            return h;
                            g = false;
                        }
                    }
                };
                var f = top.$("#tree").treeview("getSelected");
                if (a) {
                    b(f[0], c(f));
                } else {
                    b(f[0]);
                }
                top.layer.close(e);
            }
        });
    },
    selectMyOrg: function(callback,hasIds) {
        var maxDes = '<span class="text-danger mL10">限选1个</span>';
        top.layer.open({
            type: 1,
            title: "选择组织" + maxDes,
            //shadeClose: true, //开启遮罩关闭
            area: ['360px', ''], //宽高
            btn: ['确定', '取消'],
            btnAlign: 'c',
            content: '<div class="tree-container">' +
            '<div class="tree-box" style="overflow: auto;width: 350px">' +
            '<div id="tree-persons"></div>' +
            '</div>' +
            '</div>',
            success: function(layero, index) {
                var $listBox = $(layero).find('#tree-persons');
                //选择时间
                $listBox.on('click', '.msgModel', function() {
                    //选中处理
                    $(this).addClass('active').siblings().removeClass('active');
                });
                common.ajax.get("organization/back/managerorg", {}, function(data) {
                    if (data.code == "1001" && data.result.length > 0) {
                        var orglist = data.result;
                        for (var i = 0; i < orglist.length; i++) {
                            var item = orglist[i];
                            var orgName = item.organization ? item.organization.name : "无",
                                isIn = hasIds && hasIds == item._id ? 'active' : '';
                            $listBox.append('<div class="msgModel ' + isIn + '" id="top_org_' + item._id + '">' +
                                '<span class="op"><i class="fa fa-check-circle"></i></span>' +
                                '<h4 class="mB5">' + item.name + '</h4>' +
                                '</div>');
                            $listBox.find("#top_org_" + item._id).data('info', item);
                        }
                    } else {
                        common.nomalPrompt(data.result);
                    }
                })
            },
            yes: function(index, layero) {
                var res = [];
                $(layero).find(".msgModel.active").each(function() {
                    var info = $(this).data("info");
                    res.push(info);
                });
                if (res.length == 0) {
                    common.nomalPrompt('请选择组织');
                    return;
                } else {
                    callback(res);
                    top.layer.close(index);
                };
            }
        });
    },
    selectleTopOrg: function(a, d, b) {
        var c = a > 0 ? '<span class="text-danger mL10">限选1个</span>': "";
        top.layer.open({
            type: 1,
            title: "选择组织" + c,
            area: ["360px", ""],
            btn: ["确定", "取消"],
            btnAlign: "c",
            content: '<div class="tree-container"><div class="tree-box" style="overflow: auto;width: 350px"><div id="tree-persons"></div></div></div>',
            success: function(e, f) {
                var g = $(e).find("#tree-persons");
                g.on("click", ".msgModel",
                    function() {
                        var h = g.find(".msgModel.active").length;
                        if (a > 1 && a <= h) {
                            common.nomalPrompt("最多只能选择" + a + "个");
                            return;
                        }
                        if (a == 1 && !$(this).hasClass("active")) {
                            g.find(".msgModel.active").removeClass("active");
                        }
                        $(this).toggleClass("active");
                    });
                common.ajax.get("organization/back/getallfirst", {},
                    function(m) {
                        if (m.code == "1001" && m.result.length > 0) {
                            var h = m.result;
                            for (var j = 0; j < h.length; j++) {
                                var l = h[j];
                                var k = l.organization ? l.organization.name: "无",
                                    n = b && b.indexOf(l._id) >= 0 ? "active": "";
                                g.append('<div class="msgModel ' + n + '" id="top_org_' + l._id + '"><span class="op"><i class="fa fa-check-circle"></i></span><h4 class="mB5">' + l.name + '</h4><div class="date">' + (l.partyType && l.partyType.name ? l.partyType.name: "-") + "</div></div>");
                                g.find("#top_org_" + l._id).data("info", l);
                            }
                        } else {
                            common.nomalPrompt(m.result);
                        }
                    });
            },
            yes: function(f, e) {
                var g = [];
                $(e).find(".msgModel.active").each(function() {
                    var h = $(this).data("info");
                    g.push(h);
                });
                if (g.length == 0) {
                    common.nomalPrompt("请选择组织");
                    return;
                } else {
                    d(g);
                    top.layer.close(f);
                }
            }
        });
    },
    selectSameLevelPersons: function(a, d, b) {
        var c = a > 0 ? '<span class="text-danger mL10">限选1个</span>': "";
        top.layer.open({
            type: 1,
            title: "选择人员" + c,
            btn: ["确定", "取消"],
            btnAlign: "c",
            area: ["363px"],
            content: '<div class="tree-container"><div class="tree-box" style="overflow: auto;width: 350px"><div id="tree-persons"></div></div></div>',
            success: function(e, f) {
                var g = $(e).find("#tree-persons");
                g.on("click", ".msgModel",
                    function() {
                        var i = g.find(".msgModel.active").length;
                        if (a > 1 && a <= i) {
                            common.nomalPrompt("最多只能选择" + a + "个");
                            return;
                        }
                        if (a == 1 && !$(this).hasClass("active")) {
                            g.find(".msgModel.active").removeClass("active");
                        }
                        $(this).toggleClass("active");
                    });
                var h = common.userInfo && common.userInfo.subOrganization ? common.userInfo.subOrganization: "";
                common.ajax.get("user/back/findsubo/" + h, {},
                    function(n) {
                        if (n.code == "1001" && n.result.length > 0) {
                            for (var l = 0; l < n.result.length; l++) {
                                var k = n.result;
                                for (var l = 0; l < k.length; l++) {
                                    var m = k[l];
                                    var o = b && b.indexOf(m._id) >= 0 ? "active": "";
                                    var j = [];
                                    $.each(m.jobTitle,
                                        function(q, p) {
                                            p ? (j.push(p.name)) : "";
                                        });
                                    g.append('<div class="msgModel hasPic ' + o + '" id="person_' + m._id + '"><span class="op"><i class="fa fa-check-circle"></i></span><span class="headPic" style="background-image:url(\'' + common.getAllUrl(m.headPic, "/img/head-img.png") + '\')"></span><h4 class="mB5">' + m.name + '</h4><div class="date">' + j.join("/") + "</div></div>");
                                    g.find("#person_" + m._id).data("info", m);
                                }
                            }
                        } else {
                            common.nomalPrompt(n.result);
                        }
                    });
            },
            yes: function(f, e) {
                var g = [];
                $(e).find(".msgModel.active").each(function() {
                    var h = $(this).data("info");
                    g.push(h);
                });
                if (g.length == 0) {
                    common.nomalPrompt("请选择组织");
                    return;
                } else {
                    d(g);
                    top.layer.close(f);
                }
            }
        });
    },
    selectJobTitle: function(b, a) {
        top.layer.open({
            type: 1,
            title: "选择岗位",
            btn: ["确定", "取消"],
            btnAlign: "c",
            area: ["363px"],
            content: '<div class="tree-container"><div class="tree-box" style="overflow-y: auto;width: 350px;"><div id="tree-persons"></div></div></div>',
            success: function(c, d) {
                var e = $(c).find("#tree-persons");
                e.on("mouseover", ".msgModel",
                    function() {
                        $(this).addClass("hover");
                    });
                e.on("mouseout", ".msgModel",
                    function() {
                        $(this).removeClass("hover");
                    });
                common.ajax.post("jobtitle/back/select", {
                        pageNo: 1,
                        pageSize: common.ajax.MAX_VALUE
                    },
                    function(k) {
                        if (k.code == "1001") {
                            for (var g = 0; g < k.result.list.length; g++) {
                                var j = k.result.list[g];
                                var f = j.name ? j.name: "无",
                                    h = j.organization ? j.organization.name: "无",
                                    l = a && a.indexOf(j._id) >= 0 ? "active": "";
                                e.append('<div class="msgModel ' + l + '" id="jobtitle_' + j._id + '" onclick="$(this).toggleClass(\'active\')"><span class="op"><i class="fa fa-check-circle"></i></span><h4 class="mB5">' + f + '</h4><div class="date">' + h + "</div></div>");
                                e.find(":last-child").data("info", j);
                            }
                        }
                    });
            },
            yes: function(d, c) {
                var e = [];
                $(c).find(".msgModel.active").each(function() {
                    var f = $(this).data("info");
                    e.push(f);
                });
                b(e);
                top.layer.close(d);
            }
        });
    },
    selectOrgCompany: function(a, f, d, c) {
        var b = a == 1 ? "hide": "",
            e = a > 0 ? '<span class="text-danger mL10">限选1个</span>': "";
        top.layer.open({
            type: 1,
            title: "请选择组织企业" + e,
            btn: ["确定", "取消"],
            btnAlign: "c",
            area: ["900px"],
            content: '<div class="tree-container text-center"><div class="form-inline text-center mT10 mB10"><input placeholder="请输入搜索企业名称" id="__tree-persons-search-input" class="form-control mR5" style="width: 50%" /><button class="btn btn-primary" id="__tree-persons-search">搜索</button></div><div class="clearfix"><div class="tree-box text-left" style="width: 47%;"><div class="tree-title">组织树结构</div><div id="tree"></div></div><div class="tree-box text-left" style="width:47%;"><div class="tree-title" style="padding-left:10px;"><span style="float:left;">共<em class="allPeople">0</em>个</span><span style="float:left;margin-left:5px;">已加载<em class="chooseAllNum">0</em>个</span></div><div id="tree-persons"><div class="allContent"></div></div></div></div></div>',
            success: function(p, o) {
                var u = $(p).find("#tree"),
                    m = $(p).find("#tree-persons .allContent"),
                    k = $(p).find("#tree-select-persons");
                var i = "",
                    q = 1,
                    r = 20,
                    j = false,
                    s = 0,
                    g = 0,
                    n = 0,
                    l = 0;
                var v = function(y, B) {
                    console.log(B);
                    if (B.selector.indexOf("select") >= 0) {
                            A = "choosed";
                    } else {
                            A = "readyChoose";
                    }
                    var z = k.find("#choosed_" + y.id).length > 0 ? "active": "";
                    B.append('<div class="msgModel hasPic ' + z + '" id="' + A + "_" + y.id + '" style="height:40px;min-height: 40px;padding-left:5px;"><h4 class="mB5">' + y.name + '</h4></div>');
                    B.find("div:last-child").data("info", y);
                    g = $(p).find("#tree-persons .allContent").find("div.msgModel").length;
                    l = $(p).find("#tree-select-persons").find("div.msgModel").length;
                    $(p).find(".chooseAllNum").text(g);
                    $(p).find(".theChoosePeople").text(l);
                };
                $(p).find("#__tree-persons-search").click(function() {
                    var w = $(p).find("#__tree-persons-search-input").val();
                    if ($.trim(w) == "") {
                        common.nomalPrompt("搜索名称不能为空");
                        return;
                    }
                    common.ajax.post(searchPartyBranchurl, {
                            name: $.trim(w),
                            type: 1
                        },
                        function(y) {
                            m.empty();
                            if (y.status == "1") {
                                $(p).find(".allPeople").text(y.result.length);
                                for (var x = 0; x < y.result.length; x++) {
                                    v(y.result[x], m);
                                }
                            } else {
                                common.nomalPrompt(y.result);
                            }
                        });
                });
                m.on("click", ".msgModel",
                    function() {
                        var w = k.find(".msgModel").length;
                        if (a > 1 && a <= w) {
                            common.nomalPrompt("最多只能选择" + a + "人");
                            return;
                        }
                        if (a == 1) {
                            k.empty();
                            m.find(".msgModel.active").removeClass("active");
                        }
                        var y = $(this).hasClass("active"),
                            x = $(this).data("info");
                        if (y) {
                            $(this).removeClass("active");
                            k.find("#choosed_" + x._id).remove();
                        } else {
                            $(this).addClass("active");
                            v(x, k);
                        }
                    });
                var h = function(x) {
                    x.text = x.name;
                    x.nodes = x.children && x.children.length > 0 ? x.children: null;
                    if (x.nodes !== null) {
                        x.selectable = true;
                        for (var w = 0; w < x.children.length; w++) {
                            var y = x.children[w];
                            h(y);
                        }
                    }
                };
                var t = departmenturl;
                common.ajax.get(t, {},
                    function(w) {
                        if (w.status == "1") {
                            h({
                                name: "全部",
                                children: w.result
                            });
                            u.treeview({
                                color: "#676a6c",
                                multiSelect: false,
                                data: w.result,
                                showBorder: false,
                                onNodeSelected: function(z, A) {
                                    u.treeview("toggleNodeSelected", [A.nodeId, {
                                        silent: true
                                    }]);
                                    q = 1;
                                    i = A._id;
                                    common.ajax.get(ajaxPartyBranchAllurl+'/departmentId/' + A.id, {},
                                        function(C) {
                                            if (C.status == "1") {
                                                m.empty();
                                                for (var B = 0; B < C.result.length; B++) {
                                                    v(C.result[B], m);
                                                }
                                                s = $(p).find("#tree-persons .allContent").find("div.msgModel").length;
                                                $(p).find(".allPeople").text(s);
                                            } else {
                                                common.nomalPrompt(w.result);
                                            }
                                        });
                                }
                            });
                        } else {
                            common.nomalPrompt(w.result);
                        }
                    });

            },
            yes: function(h, g) {
                var l="";
                $(g).find("#tree-persons .active ").each(function(j, k) {
                    l = $(k).data("info");
                });
                f(l);
                top.layer.close(h);
            }
        });
    }, selectOrgQuarters: function(a, f, d, c) {
        var b = a == 1 ? "hide": "",
            e = a > 0 ? '<span class="text-danger mL10">限选1个</span>': "";
        top.layer.open({
            type: 1,
            title: "请选择岗位类型" + e,
            btn: ["确定", "取消"],
            btnAlign: "c",
            area: ["900px"],
            content: '<div class="tree-container text-center"><div class="form-inline text-center mT10 mB10"><input placeholder="请输入搜索岗位类型名称" id="__tree-persons-search-input" class="form-control mR5" style="width: 50%" /><button class="btn btn-primary" id="__tree-persons-search">搜索</button></div><div class="clearfix"><div class="tree-box text-left" style="width: 47%;"><div class="tree-title">组织树结构</div><div id="tree"></div></div><div class="tree-box text-left" style="width:47%;"><div class="tree-title" style="padding-left:10px;"><span style="float:left;">共<em class="allPeople">0</em>个</span><span style="float:left;margin-left:5px;">已加载<em class="chooseAllNum">0</em>个</span></div><div id="tree-persons"><div class="allContent"></div></div></div></div></div>',
            success: function(p, o) {
                var u = $(p).find("#tree"),
                    m = $(p).find("#tree-persons .allContent"),
                    k = $(p).find("#tree-select-persons");
                var i = "",
                    q = 1,
                    r = 20,
                    j = false,
                    s = 0,
                    g = 0,
                    n = 0,
                    l = 0;
                var v = function(y, B) {
                    console.log(B);
                    if (B.selector.indexOf("select") >= 0) {
                        A = "choosed";
                    } else {
                        A = "readyChoose";
                    }
                    var z = k.find("#choosed_" + y.id).length > 0 ? "active": "";
                    B.append('<div class="msgModel hasPic ' + z + '" id="' + A + "_" + y.id + '" style="height:40px;min-height: 40px;padding-left:5px;"><h4 class="mB5">' + y.name + '</h4></div>');
                    B.find("div:last-child").data("info", y);
                    g = $(p).find("#tree-persons .allContent").find("div.msgModel").length;
                    l = $(p).find("#tree-select-persons").find("div.msgModel").length;
                    $(p).find(".chooseAllNum").text(g);
                    $(p).find(".theChoosePeople").text(l);
                };
                $(p).find("#__tree-persons-search").click(function() {
                    var w = $(p).find("#__tree-persons-search-input").val();
                    if ($.trim(w) == "") {
                        common.nomalPrompt("搜索名称不能为空");
                        return;
                    }
                    common.ajax.post(searchPosturl, {
                            name: $.trim(w),
                            type: 1
                        },
                        function(y) {
                            m.empty();
                            if (y.status == "1") {
                                $(p).find(".allPeople").text(y.result.length);
                                for (var x = 0; x < y.result.length; x++) {
                                    v(y.result[x], m);
                                }
                            } else {
                                common.nomalPrompt(y.result);
                            }
                        });
                });
                m.on("click", ".msgModel",
                    function() {
                        var w = k.find(".msgModel").length;
                        if (a > 1 && a <= w) {
                            common.nomalPrompt("最多只能选择" + a + "人");
                            return;
                        }
                        if (a == 1) {
                            k.empty();
                            m.find(".msgModel.active").removeClass("active");
                        }
                        var y = $(this).hasClass("active"),
                            x = $(this).data("info");
                        if (y) {
                            $(this).removeClass("active");
                            k.find("#choosed_" + x._id).remove();
                        } else {
                            $(this).addClass("active");
                            v(x, k);
                        }
                    });
                var h = function(x) {
                    x.text = x.name;
                    x.nodes = x.children && x.children.length > 0 ? x.children: null;
                    if (x.nodes !== null) {
                        x.selectable = true;
                        for (var w = 0; w < x.children.length; w++) {
                            var y = x.children[w];
                            h(y);
                        }
                    }
                };
                var t = departmenturl;
                common.ajax.get(t, {},
                    function(w) {
                        if (w.status == "1") {
                            h({
                                name: "全部",
                                children: w.result
                            });
                            u.treeview({
                                color: "#676a6c",
                                multiSelect: false,
                                data: w.result,
                                showBorder: false,
                                onNodeSelected: function(z, A) {
                                    u.treeview("toggleNodeSelected", [A.nodeId, {
                                        silent: true
                                    }]);
                                    q = 1;
                                    i = A._id;
                                    common.ajax.get(ajaxPostAllurl+'/departmentId/' + A.id, {},
                                        function(C) {
                                            if (C.status == "1") {
                                                m.empty();
                                                for (var B = 0; B < C.result.length; B++) {
                                                    v(C.result[B], m);
                                                }
                                                s = $(p).find("#tree-persons .allContent").find("div.msgModel").length;
                                                $(p).find(".allPeople").text(s);
                                            } else {
                                                common.nomalPrompt(w.result);
                                            }
                                        });
                                }
                            });
                        } else {
                            common.nomalPrompt(w.result);
                        }
                    });

            },
            yes: function(h, g) {
                var l="";
                $(g).find("#tree-persons .active ").each(function(j, k) {
                    l = $(k).data("info");
                });
                f(l);
                top.layer.close(h);
            }
        });
    }, selectOrgPersons: function(a, f, d, c) {
        var b = a == 1 ? "hide": "",
            e = a > 0 ? '<span class="text-danger mL10">限选1人</span>': "";
        top.layer.open({
            type: 1,
            title: "请选择组织人员" + e,
            btn: ["确定", "取消"],
            btnAlign: "c",
            area: ["900px"],
            content: '<div class="tree-container text-center"><div class="form-inline text-center mT10 mB10"><input placeholder="请输入搜索人员姓名" id="__tree-persons-search-input" class="form-control mR5" style="width: 50%" /><button class="btn btn-primary" id="__tree-persons-search">搜索</button></div><div class="clearfix"><div class="tree-box text-left" style="width: 47%;"><div class="tree-title">组织树结构</div><div id="tree"></div></div><div class="tree-box text-left"><div class="tree-title" style="padding-left:10px;"><span style="float:left;">共<em class="allPeople">0</em>名</span><span style="float:left;margin-left:5px;">已加载<em class="chooseAllNum">0</em>名</span><span class="chooseAll ' + b + '"><i class="fa fa-check-circle mR5"></i>全选</span></div><div id="tree-persons"><div class="allContent"></div></div></div><div class="tree-box text-left" style="margin-right: 0;"><div class="tree-title"><span style="float:left;margin-left:10px;">已选<em class="theChoosePeople">0</em>名</span><span class="removeAll"><i class="fa fa-trash mR5"></i>清除</span></div><div id="tree-select-persons"></div></div></div></div>',
            success: function(p, o) {
                var u = $(p).find("#tree"),
                    m = $(p).find("#tree-persons .allContent"),
                    k = $(p).find("#tree-select-persons");
                var i = "",
                    q = 1,
                    r = 20,
                    j = false,
                    s = 0,
                    g = 0,
                    n = 0,
                    l = 0;
                var v = function(y, B) {
                    if (B.selector.indexOf("select") >= 0) {
                        var x = '<i class="fa fa-trash"></i>',
                            A = "choosed";
                    } else {
                        var x = '<i class="fa fa-check-circle"></i>',
                            A = "readyChoose";
                    }
                    var z = k.find("#choosed_" + y._id).length > 0 ? "active": "";
                    var w = [];
                    // $.each(y.jobTitle,
                    //     function(D, C) {
                    //         C ? (w.push(C.name)) : "";
                    //     });
                    B.append('<div class="msgModel hasPic ' + z + '" id="' + A + "_" + y.id + '"><span class="op">' + x + '</span><span class="headPic"></span><h4 class="mB5">' + y.truename + '</h4><div class="date">' + w.join("/") + "</div></div>");
                    B.find(":last-child").data("info", y);
                    g = $(p).find("#tree-persons .allContent").find("div.msgModel").length;
                    l = $(p).find("#tree-select-persons").find("div.msgModel").length;
                    $(p).find(".chooseAllNum").text(g);
                    $(p).find(".theChoosePeople").text(l);
                };
                if (d && d.length > 0) {
                    a == 1 ? d = [d] : "";
                    $.each(d,
                        function(w, x) {
                            common.ajax.get(ajaxMemberAllurl + x, {},
                                function(y) {
                                    if (y.status == "1") {
                                        v(y.result, k);
                                    }
                                });
                        });
                }
                $(p).find("span.chooseAll").click(function() {
                    m.find(".msgModel").each(function(w, x) {
                        var y = $(x).hasClass("active");
                        if (!y) {
                            $(this).click();
                        }
                    });
                });
                $(p).find("span.removeAll").click(function() {
                    k.find(".op").each(function(w, x) {
                        $(this).click();
                    });
                });
                $(p).find("#__tree-persons-search").click(function() {
                    var w = $(p).find("#__tree-persons-search-input").val();
                    if ($.trim(w) == "") {
                        common.nomalPrompt("搜索姓名不能为空");
                        return;
                    }
                    common.ajax.post("user/back/selectbyname", {
                            name: $.trim(w),
                            type: 1
                        },
                        function(y) {
                            m.empty();
                            if (y.code == "1001") {
                                $(p).find(".allPeople").text(y.result.length);
                                for (var x = 0; x < y.result.length; x++) {
                                    v(y.result[x], m);
                                }
                            } else {
                                common.nomalPrompt(y.result);
                            }
                        });
                });
                m.on("click", ".msgModel",
                    function() {
                        var w = k.find(".msgModel").length;
                        if (a > 1 && a <= w) {
                            common.nomalPrompt("最多只能选择" + a + "人");
                            return;
                        }
                        if (a == 1) {
                            k.empty();
                            m.find(".msgModel.active").removeClass("active");
                        }
                        var y = $(this).hasClass("active"),
                            x = $(this).data("info");
                        if (y) {
                            $(this).removeClass("active");
                            k.find("#choosed_" + x._id).remove();
                        } else {
                            $(this).addClass("active");
                            v(x, k);
                        }
                    });
                m.on("mouseover", ".msgModel",
                    function() {
                        $(this).addClass("hover");
                    });
                m.on("mouseout", ".msgModel",
                    function() {
                        $(this).removeClass("hover");
                    });
                k.on("click", ".op",
                    function() {
                        var w = $(this).parents(".msgModel"),
                            x = w.data("info");
                        w.remove();
                        m.find("#readyChoose_" + x._id).removeClass("active");
                        l = $(p).find("#tree-select-persons").find("div.msgModel").length;
                        $(p).find(".theChoosePeople").text(l);
                    });
                var h = function(x) {
                    x.text = x.name;
                    x.nodes = x.children && x.children.length > 0 ? x.children: null;
                    if (x.nodes !== null) {
                        x.selectable = true;
                        for (var w = 0; w < x.children.length; w++) {
                            var y = x.children[w];
                            h(y);
                        }
                    }
                };
                var t = departmenturl;
                common.ajax.get(t, {},
                    function(w) {
                        if (w.status == "1") {
                            h({
                                name: "全部",
                                children: w.result
                            });
                            u.treeview({
                                color: "#676a6c",
                                multiSelect: true,
                                data: w.result,
                                showBorder: false,
                                onNodeSelected: function(z, A) {
                                    u.treeview("toggleNodeSelected", [A.nodeId, {
                                        silent: true
                                    }]);
                                    q = 1;
                                    i = A._id;
                                    var y = $(p).find("span.queryAll i").css("color");
                                    common.ajax.get(ajaxMemberAllurl+"/departmentId/" + A.id, {},
                                        function(C) {
                                            if (C.status == "1") {
                                                m.empty();
                                                for (var B = 0; B < C.result.length; B++) {
                                                    v(C.result[B], m);
                                                }
                                                s = $(p).find("#tree-persons .allContent").find("div.msgModel").length;
                                                $(p).find(".allPeople").text(s);
                                            } else {
                                                common.nomalPrompt(w.result);
                                            }
                                        });
                                }
                            });
                        } else {
                            common.nomalPrompt(w.result);
                        }
                    });
            },
            yes: function(h, g) {
                var i = [];
                $(g).find("#tree-select-persons .msgModel").each(function(j, k) {
                    var l = $(k).data("info");
                    i.push(l);
                });
                f(i);
                top.layer.close(h);
            }
        });
    },
    initPictures: function(c, b) {
        if (typeof b === "string") {
            b = [b];
        }
        if (! (b instanceof Array)) {
            return;
        }
        $(c).addClass("lightBoxGallery").html("");
        for (var a = 0; a < b.length; a++) {
            $(c).append('<a href="' + common.getAllUrl(b[a]) + '" title="图片" data-gallery="" style="padding-right: 10px;"><img width="100" src="' + common.getAllUrl(b[a]) + '"></a>');
        }
        $("body").append('<div id="blueimp-gallery" class="blueimp-gallery"><div class="slides"></div><h3 class="title"></h3><a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a><ol class="indicator"></ol></div>');
        blueimp.Gallery($("body")[0], {
            container: c,
            carousel: true,
            startSlideshow: true,
            stretchImages: true,
            hidePageScrollbars: false,
            disableScroll: false
        });
    },
    getMinuteTime: function(a) {
        if (!a || (a.indexOf("-") < 0 && a.indexOf("/") < 0)) {
            return "";
        }
        a = a.replace(/-/g, "/");
        return new Date(a).format("yyyy-MM-dd hh:mm");
    },
    getSecondsTime: function(a) {
        if (!a || (a.indexOf("-") < 0 && a.indexOf("/") < 0)) {
            return "";
        }
        a = a.replace(/-/g, "/");
        return new Date(a).format("yyyy-MM-dd hh:mm:ss");
    },
    decimalTimeFormat: function(d) {
        var e = parseInt(d),
            c = parseInt((d - e) * 60),
            a = Math.round(((d - e) * 60 - c) * 60),
            b = "";
        e > 0 ? b += e + "小时": "";
        c > 0 ? b += c + "分": "";
        a > 0 ? b += a + "秒": "";
        return b == "" ? "0秒": b;
    },
    getUserType: function() {
        var c = common.userInfo;
        var b = 99999;
        if (c.loginOrg) {
            for (var a = 0; a < c.loginOrg.length; a++) {
                var d = c.loginOrg[a];
                if (d.type < b) {
                    b = d.type;
                }
            }
        }
        if (c.loginOrg && c.loginOrg.length == 0) {
            return 3;
        }
        if (b == 99999) {
            return 0;
        } else {
            return b;
        }
    },
    wordsNum: function(b, c, a) {
        var d = b ? b.length: 0;
        if (a && d < a) {
            c.html("最少字数<b>" + a + "</b>，已输入<b>" + d + "</b>，还需输入<b>" + (a - d) + "</b>").addClass("text-danger");
        } else {
            if (a && d >= a) {
                c.html("最少字数<b>" + a + "</b>，已输入<b>" + d + "</b>，已达要求").removeClass("text-danger");
            } else {
                c.html("不限字数，已输入<b>" + d + "</b>").removeClass("text-danger");
            }
        }
    },
    printme: function() {
        var a = "";
        a = document.body.innerHTML;
        $(this).parent().addClass("hide");
        window.print();
        $(this).parent().removeClass("noprint");
        window.setTimeout(function() {
                document.body.innerHTML = a;
            },
            1500);
    },
    derive: function(a) {
        top.layer.confirm("确定要导出吗？", {
                btn: ["确定 ", "取消"],
                title: "提示",
                icon: 0,
                closeBtn: false,
                shade: ["0.3", "#000"]
            },
            function() {
                common.loadBox.add("图片生成中，请耐心等待", 300000);
                html2canvas(document.getElementById("ep"), {
                    onrendered: function(b) {
                        common.convertCanvasToImage(b, a);
                    },
                    background: "#ffffff"
                });
            },
            function() {});
    },
    selfCheck: function(b) {
        var a = common.userInfo;
        if (b == a._id) {
            return true;
        }
        return false;
    },
    roleCheck: function(b) {
        var a = common.userInfo;
        if (b == "a" && a.mobile == "admin") {
            return true;
        }
        if (b == "mx" && a.isMaxManager == "1") {
            return true;
        }
        if (b == "m" && a.isManager == "1") {
            return true;
        }
        if (b == "p" && a.isManager != "1" && a.mobile != "admin") {
            return true;
        }
        return false;
    },
    initBtnShow: function(c, b) {
        b = b.split(",");
        c = c.split(",");
        var a = false;
        $.each(c,
            function(d, e) {
                if (common.roleCheck(e)) {
                    a = true;
                    return false;
                }
            });
        $.each(b,
            function(d, e) {
                if (a) {
                    $(e).removeClass("hide");
                } else {
                    $(e).remove();
                }
            });
    },
    initPaging: function(total, pageSize, currentPage, element, callback) {
        if (total == 0) {
            total = 1;
        };
        !pageSize ? pageSize = 10 : '';
        !currentPage ? currentPage = 1 : '';
        var count = Math.ceil(total / pageSize);
        var options = {
            alignment: "left",
            bootstrapMajorVersion: 3,
            currentPage: currentPage,
            numberOfPages: pageSize,
            totalPages: count,
            onPageClicked: function(event, originalEvent, type, page) {
                if (typeof callback == 'function') {
                    callback(page)

                }
            },
            onInit: function() {
                $(element).append('<li class="f12 line30 mL10">共 ' + count + ' 页，' + total + ' 条数据</li>');
            }
        }
        //初始化翻页控件
        $(element).bootstrapPaginator(options);


    },
    itemDel: function(e, d, a, c, b) {
        b ? stopBubble(b) : "";
        top.layer.confirm("确定要删除吗？", {
                btn: ["确定", "取消"],
                title: "提示",
                icon: 0,
                closeBtn: false,
                shade: ["0.3", "#000"]
            },
            function() {
                common.ajax.get(d + "/back/" + a + "/" + e, {},
                    function(f) {
                        if (f.code == "1001") {
                            common.nomalPrompt("删除成功", c);
                        } else {
                            if (f.code == "1002") {
                                common.nomalPrompt(f.message);
                            } else {
                                common.nomalPrompt(f.result);
                            }
                        }
                    });
            });
    },
    itemPublish: function(d, c, a, b) {
        top.layer.confirm("发布后无法删除，确定要发布吗？", {
                btn: ["确定", "取消"],
                title: "提示",
                icon: 0,
                closeBtn: false,
                shade: ["0.3", "#000"]
            },
            function() {
                common.ajax.get(c + "/back/" + a + "/" + d, {},
                    function(e) {
                        if (e.code == "1001") {
                            common.nomalPrompt("发布成功", b);
                        } else {
                            if (e.code == "1002") {
                                common.nomalPrompt(e.message);
                            } else {
                                common.nomalPrompt(e.result);
                            }
                        }
                    });
            });
    },
    noItem: function(c, d, a) {
        a = a || 90;
        var b = d.parents("html").height() - a;
        d.html('<div class="text-center no-data" style="height:' + b + 'px;"><div class="main"><img class="inline" src="../../img/nodata.png"><p style="margin-top:20px;font-size:20px">' + c + "</p></div></div>");
    },
    nomalPrompt: function(a, b) {
        if (typeof a != "string") {
            a = "暂无";
        }
        top.layer.msg(a, {
                time: 2000,
                shade: ["0", "#000"]
            },
            function() {
                if (typeof b == "function") {
                    b(true);
                }
            });
    },
    getAllUrl: function(d, c) {
        if (!d) {
            c = c || "/img/timg.jpg";
            return config.siteUrl + c;
        }
        var b = /^[a-zA-z]+:\/\/[^\s]*$/;
        if (b.test(d)) {
            return d;
        }
        var a = /^\//;
        if (a.test(d)) {
            return config.updateFileUrl + d;
        }
        return config.updateFileUrl + "/" + d;
    },
    writeUploadPic: function(b, a) {
        a.before('<li imgUrl="' + b + '" style="background-image:url(' + b + ');"><i class="fa fa-times-circle"></i></li>');
    },
    writeFile: function(e, a) {
        for (var c = 0; c < e.length; c++) {
            var f = e[c];
            if (!f.describe || f.describe == undefined) {
                continue;
            }
            var d = /^[a-zA-z]+:\/\/[^\s]*$/;
            if (d.test(f.url)) {
                var b = '<a href="' + f.url + '" target="_black">' + f.describe + "</a>";
            } else {
                var b = '<a download="' + f.url + '" href="' + common.getAllUrl(f.url) + '">' + f.describe + "</a>";
            }
            a.append('<div class="mB5">' + b + "</div>");
        }
    },
    delPermCheck: function(d, a, g, b) {
        var f = common.roleCheck("m"),
            e = b ? b.substr(1, 1) : "";
        if (f && (a == "1" || g == "1")) {
            return true;
        }
        if (!f && g == "1" && d != e) {
            return true;
        }
        return false;
    },
    selectCallback: function(d, g, c, a) {
        var f = function(k, h) {
            c == "value" ? g.val(k) : g.text(k);
            g.data("id", h);
            g.blur();
            var j = document.createElement("span");
            j.innerText = k;
            j.style.visibility = "hidden";
            document.body.appendChild(j);
            var i = j.offsetWidth + 18;
            g[0].offsetWidth < i ? g.css("width", i) : "";
            j.parentNode.removeChild(j);
        };
        console.log(a);
        if (d && a) {
            var b = [];
            var e = [];
            $.each(d,
                function(h, j) {
                    b.push(j._id);
                    e.push(j.name);
                });
            f(e.join("，"), b);
        } else {
            if (d && !a) {
                if (d instanceof Array && d.length > 0) {
                    f(d[0].name, d[0]._id);
                } else {
                    f(d.name, d._id);
                }
                if (!d || d.length == 0) {
                    g.removeData("id");
                    g.val("");
                }
            }
        }
    },
    getDuration: function(g, b) {
        if (b == undefined || b == null || b == "null" || b == "undefined") {
            b = new Date();
        } else {
            b = new Date(b.replace(/-/g, "/"));
        }
        g = new Date(g.replace(/-/g, "/"));
        var f = b - g,
            e = parseInt(f / 1000 / 60 / 60),
            a = parseInt(f / 1000 / 60) - 60 * e,
            d = parseInt(f / 1000) - 60 * a - 60 * 60 * e,
            c = "";
        e > 0 ? c += e + "小时": "";
        a > 0 ? c += a + "分钟": "";
        d > 0 ? c += d + "秒": "";
        return c;
    },
    loadBox: {
        thisLayerIndex: 9999,
        add: function(a) {
            thisLayerIndex = top.layer.msg('<div class="text-center"><img src="/public/statics/images/loading-bars.svg" width="50" height="32">' + a + "</div>", {
                skin: "layer-loading",
                time: 6000 * 1000,
                shade: ["0", "#000"]
            });
        },
        hide: function() {
            top.layer.close(thisLayerIndex);
        }
    },
    dataFomate: {
        score: function(b, c) {
            b = parseFloat(b) || 0;
            var a = Math.abs(b);
            a = parseFloat(a).toFixed(2);
            a = a.toString();
            if (c) {
                a = a.split(".");
            }
            return [a, (b >= 0 ? "+": "-")];
        }
    },
    textareaVal: {
        transcoding: function(a) {
            a = a.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
            a = a.replace(/[\r\n]/gi, "<br/>").replace(/[ ]/g, "&nbsp;");
            return a;
        },
        reduction: function(a) {
            a = a.replace(/<br\/>/gi, "\r\n").replace(/&nbsp;/g, " ");
            a = a.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"');
            return a;
        },
    }
};
$.fn.getAddress = function() {
    var b = $(this);
    b.attr("placeholder", "地址获取中，请稍等...");
    var c = function() {
        AMap.plugin("AMap.Geolocation",
            function() {
                var d = new AMap.Geolocation({
                    enableHighAccuracy: true,
                    timeout: 10000,
                    convert: true,
                    buttonOffset: new AMap.Pixel(10, 20),
                    panToLocation: true,
                    zoomToAccuracy: true,
                    buttonPosition: "RB"
                });
                d.getCurrentPosition(function(h, g) {
                    console.log(g);
                });
                AMap.event.addListener(d, "complete", f);
                AMap.event.addListener(d, "error", e);
                function f(g) {
                    a(g.position.getLng(), g.position.getLat());
                }
                function e(g) {
                    var h = "定位失败。";
                    h += "错误信息：";
                    switch (g.info) {
                        case "PERMISSION_DENIED":
                            h += "您阻止了定位操作";
                            break;
                        case "POSITION_UNAVAILBLE":
                            h += "无法获得当前位置";
                            break;
                        case "TIMEOUT":
                            h += "定位超时";
                            break;
                        default:
                            h += "未知错误";
                            break;
                    }
                    b.attr("placeholder", "定位失败").siblings("span.help-block").html('<i class="fa fa-exclamation-circle"></i> ' + h + "，您可以使用“位置校准”手动设置").removeClass("hide");
                }
            });
    } ();
    function a(d, f) {
        var e = [d, f];
        AMap.plugin("AMap.Geocoder",
            function() {
                var g = new AMap.Geocoder({
                    city: "010"
                });
                g.getAddress(e,
                    function(i, h) {
                        if (i == "complete") {
                            b.val(h.regeocode.formattedAddress);
                        } else {
                            b.attr("placeholder", "暂时无法获取地址信息，请稍后再试！");
                        }
                    });
            });
    }
};
$('input[data-map="1"]').each(function() {
    var b = $(this),
        a = top.$(window).outerWidth() - 300;
    bodyHeight = top.$(window).outerHeight() - 240;
    b.on("click",
        function() {
            var c = '<div class="modal inmodal fade" id="mapModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"><div class="modal-dialog modal-lg" style="width:' + a + 'px;"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button><h6 class="modal-title">选择地点</h6></div><div class="modal-body"><iframe style="width:100%;height:' + bodyHeight + 'px;" src="html/map.html" frameborder="0" border="0"></iframe></div><div class="modal-footer" style="text-align:center;"><button type="button" class="btn btn-default" data-dismiss="modal">取消</button><button type="button" class="btn btn-primary">确认</button></div>';
            "</div></div></div>";
            top.$("body").append(c);
            top.$("#mapModal").modal("show").data({}).on("hide.bs.modal",
                function(f) {
                    var d = f.target.id;
                    if (d == "mapModal") {
                        top.$("#mapModal").remove();
                    }
                });
        });
});
$.fn.getLnglat = function(c, g) {
    var b = $(this),
        a = top.$(window).outerWidth() - 300,
        f = top.$(window).outerHeight() - 240;
    a = a < 1000 ? "90%": a + "px";
    var e = {
        $address: null
    };
    var d = $.extend({},
        e, c);
    b.on("click",
        function() {
            var j = $(this).data("val"),
                i = d.$address ? d.$address.val() : "";
            if (j) {
                var h = j.indexOf(",") >= 0 ? j.split(",") : "";
                top.$("body").data("getLnglat", {
                    lng: h[0],
                    lat: h[1]
                });
            } else {
                top.$("body").removeData("getLnglat");
            }
            if (i) {
                top.$("body").data("getAddress", {
                    address: d.$address.val()
                });
            } else {
                top.$("body").removeData("getAddress");
            }
            top.layer.open({
                type: 1,
                title: "请选择坐标",
                btn: ["确定", "取消"],
                btnAlign: "c",
                area: [a, f + 200],
                content: '<iframe style="width:100%;height:' + f + 'px;" src="'+mapurl+'" frameborder="0" border="0"></iframe>',
                yes: function(k, l) {
                    var m = top.$("body").data("getLnglat");
                    if (m && typeof g === "function") {
                        g(b, m);
                        top.layer.closeAll();
                    } else {
                        common.nomalPrompt("请选择坐标");
                    }
                },
            });
        });
};
$.fn.uploadFiles = function(b, g) {
    var a = $(this);
    var f = {
        btnText: "添加",
        describe: false,
        link: false,
        files: []
    };
    var d = $.extend({},
        f, b);
    a.data("files", []);
    a.addClass("upload-files-box").html('<span class="btn btn-default mR5 add-btn">' + d.btnText + '</span><input type="file" class="hide"><div class="file-show"></div>');
    if (d.files.length > 0) {
        $.each(d.files,
            function(j, k) {
                var h = k.describe ? k.describe: k.substring(k.lastIndexOf("/") + 1);
                e(h, k);
            });
        a.data("files", d.files);
    }
    function e(h, i) {
        var j = a.data("files");
        j.push(i);
        a.data("files", j);
        a.find("div.file-show").append('<span class="tag">' + h + '<i class="fa fa-close"></i></span>');
    }
    a.on("click", "span.add-btn",
        function() {
            if (d.describe) {
                var h = '<div class="modal inmodal fade" id="addFilesModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button><h6 class="modal-title">添加资料</h6></div><div class="modal-body"><p class="text-danger"><i class="fa fa-exclamation-circle"></i> 请注意，“从本地上传”和“资料地址”任选其一<br/>&nbsp;&nbsp;&nbsp;&nbsp;支持小于10M的Word、PowerPoint和PDF，小于5M的Excel</p><div class="form-group note-form-group note-group-select-from-files"><label class="note-form-label">显示文本</label><input class="note-image-input form-control note-form-control note-input" type="text" id="sourceName"></div><div class="form-group note-form-group note-group-select-from-files"><label class="note-form-label">从本地上传</label><input class="note-image-input form-control note-form-control note-input" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.ms-excel,application/msword,application/pdf" name="files" id="sourceIpt"></div><div class="form-group note-group-image-url" style="overflow:auto;"><label class="note-form-label">资料地址</label><input class="note-image-url form-control note-form-control note-input  col-md-12" type="text" id="sourceLink"><div class="clearfix"></div><span class="help-block">输入格式如：https://szct.qiantongyun.cn/ 或 http://szct.qiantongyun.cn/</span></div></div><div class="modal-footer"><button id="writeSelectFile" class="btn btn-primary note-btn note-btn-primary note-image-btn">插入资料</button></div></div></div></div>';
                top.$("body").append(h);
                top.$("#addFilesModal").modal("show").data({}).on("hide.bs.modal",
                    function(k) {
                        var i = k.target.id;
                        if (i == "addFilesModal") {
                            var j = top.$("#addFilesModal");
                            j.remove();
                        }
                    }).on("shown.bs.modal",
                    function(j) {
                        var i = $(j.target);
                        i.find("#sourceIpt").on("change",
                            function() {
                                var m = $(this),
                                    l = m[0].files[0],
                                    p = l.name,
                                    k = l.size,
                                    n = /.*.xlsx$|.*.xls$|.*.XLSX$|.*.XLS$/,
                                    o = /.*.docx$|.*.doc$|.*.xlsx$|.*.xls$|.*.pptx$|.*.ppt$|.*.pdf$|.*.DOCX$|.*.DOC$|.*.XLSX$|.*.XLS$|.*.PPTX$|.*.PPT$|.*.PDF$/;
                                if (!o.test(p)) {
                                    common.nomalPrompt("请上传word、excel、ppt、pdf类型的文件");
                                    m.val("");
                                    return;
                                }
                                if (n.test(p) && k > 1024 * 1024 * 5) {
                                    common.nomalPrompt("Excel不能超过5M");
                                    m.val("");
                                    return true;
                                }
                                if (!n.test(p) && k > 1024 * 1024 * 10) {
                                    common.nomalPrompt("Word、PowerPoint和PDF不能超过10M");
                                    m.val("");
                                    return true;
                                }
                                i.find("#sourceLink").attr("disabled", "disabled");
                                c(l,
                                    function(r, q) {
                                        i.data("file", q);
                                    });
                            });
                        i.find("#sourceLink").on("keyup",
                            function() {
                                $(this).val() ? i.find("#sourceIpt").attr("disabled", "disabled") : i.find("#sourceIpt").removeAttr("disabled");
                            });
                        i.find("#writeSelectFile").on("click",
                            function() {
                                var l = i.find("#sourceName").val(),
                                    k = i.data("file"),
                                    n = i.find("#sourceLink").val();
                                if (!l) {
                                    common.nomalPrompt("请输入显示文本");
                                    return;
                                }
                                var m = /^[a-zA-z]+:\/\/[^\s]*$/;
                                if (n && !m.test(n)) {
                                    common.nomalPrompt("资料地址格式不正确");
                                    return;
                                }
                                if (!k && !n) {
                                    common.nomalPrompt("请上传资料或者填写资料地址");
                                    return;
                                }
                                top.$("#addFilesModal").modal("hide");
                                e(l, {
                                    describe: l,
                                    url: k ? k: n
                                });
                            });
                    });
            } else {
                a.find("input").click();
            }
        });
    a.on("change", "input",
        function() {
            var h = $(this),
                i = h[0].files;
            if (i.length > 0) {
                c(i[0],
                    function(k, j) {
                        e(k, j);
                        h.val("");
                    });
            }
        });
    function c(h, j) {
        var i = new FormData();
        i.append("upfile", h);
        common.loadBox.add("上传中，请稍后");
        common.ajax.upload("upload/file", i,
            function(k) {
                k = JSON.parse(k);
                common.loadBox.hide();
                if (k.code == "1001") {
                    j(h.name, k.result);
                } else {
                    if (k.code == "1002") {
                        common.nomalPrompt(k.message);
                    } else {
                        common.nomalPrompt(k.result);
                    }
                }
            },
            function() {
                common.nomalPrompt("请检查文件命名是否规范",
                    function() {
                        common.loadBox.hide();
                    });
            });
    }
    a.on("click", "i.fa-close",
        function() {
            var i = a.data("files"),
                h = $(this).parent();
            i.splice(h.index(), 1);
            a.data("files", i);
            h.remove();
        });
};
$("div.result-box").on("mouseover", ".list",
    function(c) {
        var a = $(c.target),
            b = a.hasClass("list") ? a: a.parents(".list.pdiv"),
            d = b.find(".flag");
        d.removeClass("hide");
    });
$("div.result-box").on("mouseout", ".list",
    function(c) {
        var a = $(c.target),
            b = a.hasClass("list") ? a: a.parents(".list.pdiv"),
            d = b.find(".flag");
        d.addClass("hide");
    });
$(".upload-pic-btn").each(function() {
    var c = $(this).find("input[type=file]"),
        b = c.attr("accept").indexOf("image") >= 0;
    if (b) {
        var a = c.attr("number");
        if (a == "" || a == 0 || a > 1) {
            c.attr("multiple", "multiple");
        }
    }
    $(this).off("click").on("click",
        function() {
            c.UploadImg({
                url: uploadimgurl,
                maxwidth: "1000",
                quality: "0.8",
                mixsize: "10485760",
                element: c,
                files: [],
                before: function(d) {
                    common.loadBox.add("图片上传中，请稍后");
                },
                error: function(d) {
                    common.nomalPrompt(d,
                        function() {
                            common.loadBox.hide();
                        });
                },
                success: function(d) {
                    console.log(d);
                    c.val("");
                    common.loadBox.hide();
                    var e = c.parent("li");
                    a == 1 && e.prev().length > 0 ? e.prev().remove() : "";
                    console.log(e);
                    $.each(d,
                        function(f, g) {
                            if (g.url) {
                                common.writeUploadPic(g.url, e);
                            }
                            a == 1 ? e.addClass("edit") : "";
                        });
                }
            });
        });
});
$(".upload-picture-box").on("click", "li i.fa-times-circle",
    function() {
        var a = $(this).parent();
        a.siblings("li.upload-pic-btn").removeClass("edit");
        a.remove();
    });
$.each($("#summernote"),
    function() {
        var a = $(this);
        a.summernote({
            lang: "zh-CN",
            placeholder: "请输入文字内容",
            minHeight: 200,
            dialogsFade: true,
            dialogsInBody: true,
            disableDragAndDrop: false,
            fontNames: ["微软雅黑", "宋体", "黑体", "楷体", "幼圆", "隶书", "Arial", "Comic Sans MS", "Courier New"],
            fontNamesNormal: ["微软雅黑"],
            toolbar: [["style", ["style"]], ["font", ["bold", "underline", "clear"]], ["fontname", ["fontname"]], ["color", ["color"]], ["para", ["ul", "ol", "paragraph"]], ["table", ["table"]], ["insert", ["link", "picture"]], ["view", ["fullscreen", "codeview", "help"]]],
            callbacks: {
                onImageUpload: function(b) {
                    a.UploadImg({
                        url: config.apiUrl + "upload/image",
                        maxwidth: "1000",
                        quality: "0.8",
                        mixsize: "10485760",
                        files: b,
                        before: function(c) {
                            common.loadBox.add("图片上传中，请稍后");
                        },
                        error: function(c) {
                            common.nomalPrompt(c,
                                function() {
                                    common.loadBox.hide();
                                });
                        },
                        success: function(c) {
                            common.loadBox.hide();
                            $.each(c,
                                function(d, e) {
                                    if (e.url) {
                                        a.summernote("insertImage", common.getAllUrl(e.url),
                                            function(f) {
                                                f.css({
                                                    "max-width": "100%",
                                                    width: f.width(),
                                                });
                                                f.attr("alt", e.name);
                                            });
                                    }
                                });
                        }
                    });
                }
            },
        });
    });
$("ul.multiple-choice").on("click", "li",
    function(a) {
        $(this).toggleClass("active");
    });
$.each($("select.auto-init"),
    function(b) {
        var d = $(this),
            c = d.attr("config").split("."),
            f = d.attr("normal-val"),
            e = 0;
        var a = function(h) {
            var g = "";
            g = h[c[e]];
            e++;
            if (e >= c.length) {
                common.initOptions(d, g, f);
            } else {
                a(g);
            }
        };
        a(config);
    });
$.each($("input.popup-content-selection"),
    function(e) {
        $(this).attr("readonly", "readonly");
        var d = $(this).attr("popup"),
            c = parseInt($(this).attr("max")),
            b = $(this).attr("placeholder"),
            a = !c ? "不限": "限" + c + "个",
            f = {
                topOrg: "请选择组织",
                singleOrg: "请选择组织",
                jobTitle: "请选择岗位(可多选)",
                multiAreas: "请选择纪实地点(可多选)",
                singleAera: "请选择纪实地点",
                sameLevelPerson: "请选择人员",
                persons: "请选择人员(" + a + ")",
                managePersons: "请选择人员(" + a + ")",
            }; ! b ? $(this).attr("placeholder", f[d]) : "";
        $(this).on("click",
            function() {
                var h = $(this),
                    j = h.attr("popup"),
                    g = h.attr("max"),
                    i = h.data("id");
                g = g || 0;
                switch (j) {
                    case "jobTitle":
                        common.selectJobTitle(function(k) {
                                common.selectCallback(k, h, "value", true);
                            },
                            i);
                        break;
                    case "topOrg":
                        common.selectleTopOrg(g,
                            function(k) {
                                var l = g == 1 ? false: true;
                                common.selectCallback(k, h, "value", l);
                            },
                            i);
                        break;
                    case "singleOrg":
                        common.selectSingleOrg(function(k) {
                            common.selectCallback(k, h, "value", false);
                        });
                        break;
                    case "multiAreas":
                        common.selectMultiAreas(function(k) {
                            common.selectCallback(k, h, "value", true);
                        });
                        break;
                    case "singleAera":
                        common.selectSingleAera(function(k) {
                            common.selectCallback(k, h, "value", false);
                        });
                        break;
                    case "sameLevelPerson":
                        common.selectSameLevelPersons(function(k) {
                            common.selectCallback(k, h, "value", false);
                        });
                        break;
                    case "persons":
                        common.selectOrgPersons(g,
                            function(k) {
                                var l = g == 1 ? false: true;
                                common.selectCallback(k, h, "value", l);
                            },
                            i);
                        break;
                        case "partyBranch":
                            common.selectOrgCompany(g,function(k) {
                                common.selectCallback(k, h, "value", false);
                            });
                        break;
                    case "typeQuarters":
                        common.selectOrgQuarters(g,function(k) {
                            common.selectCallback(k, h, "value", false);
                        });
                        break;
                    case "managePersons":
                        common.selectOrgPersons(0,
                            function(k) {
                                common.selectCallback(k, h, "value", true);
                            },
                            i, "manage");
                        break;
                }
            });
    });
var formCheck = function(f) {
    var a = [false, ""],
        d = {
            name: {
                reg: /^[A-Za-z0-9_\-\u4e00-\u9fa5]+$/,
                error: "必须为中文、字母、数字、“-”“_”的组合，1-20个字符"
            },
            mobile: {
                reg: /^(1\d{10}|admin)$/,
                error: "输入不正确"
            },
            phone: {
                reg: /^(\d{3}-\d{8}|\d{4}-\d{7,8})$|^(0?(13|14|15|17|18)[0-9]{9})$/,
                error: "输入不正确"
            },
            password: {
                reg: /^[0-9a-zA-Z!@$#%*,.;\'\|\{\}\[\]\-\_\+\/\\]{6,16}$/,
                error: "必须为6~16位的数字、字母（区分大小写）、符号组成"
            },
            acount: {
                reg: /^[0-9a-zA-Z\_\-]{4,20}$/,
                error: "必须为字母、数字、“-”“_”的组合，4-20个字符"
            },
            "num-and-ler": {
                reg: /^[0-9a-zA-Z]{4,20}$/,
                error: "必须为字母、数字的组合，4-20个字符"
            },
            email: {
                reg: /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/,
                error: "输入不正确"
            },
            "positive-number": {
                reg: /^([0-9]+(\.[0-9]{1})?|0\.[1-9])$/,
                error: "只能为大于或等于零的数字(支持一位小数点)"
            },
            number: {
                reg: /^[0-9]+\.{0,1}[0-9]{0,1}$/,
                error: "只能为大于或等于零的数字"
            },
            integer: {
                reg: /^[0-9]\d*$/,
                error: "只能为正整数"
            },
            "integer-and-zero": {
                reg: /^([1-9]\d*|0)$/,
                error: "只能为正整数或零"
            },
        };
    for (var c = 0; c < f.length; c++) {
        var g = $.trim(f[c].value),
            e = f[c].text,
            b = d[f[c].field];
        if (g == undefined || g == "" || g == null) {
            e = (f[c].field == "select" ? "请选择": "请输入") + e;
            a = [false, e];
            break;
        } else {
            if (b && !b.reg.test(g)) {
                a = [false, e + b.error];
                break;
            } else {
                a = [true, "验证通过"];
            }
        }
    }
    return a;
};
$("body").on("blur", ".form-check",
    function(a) {
        var f = $(a.target),
            c = f.val(),
            i = f.parents(".form-check-item"),
            d = f.attr("errortext"),
            g = i.find(".help-block"),
            b = f.attr("check"),
            e = g.hasClass("hide");
        var h = formCheck([{
            field: b,
            text: d,
            value: c
        }]);
        if (!h[0]) {
            i.addClass("has-error");
            f.removeClass("checked");
            g.html('<i class="fa fa-times-circle"></i> ' + h[1]).removeClass("hide");
        } else {
            i.removeClass("has-error");
            f.addClass("checked");
            g.addClass("hide");
        }
    });
var checkedSuss = function(d, g, b) {
    var f = $(d).parents(".form-check-box"),
        a = f.find(".form-check-item.has-error").length > 0;
    var e = true,
        c = "";
    $.each(f.find(".form-check"),
        function(h) {
            $(this).blur();
            e = $(this).hasClass("checked");
            if (!e) {
                c = $(this).siblings(".help-block").text();
                return false;
            }
        });
    if (!e) {
        common.nomalPrompt(c);
        return false;
    }
    if (typeof g == "function") {
        g(b);
    }
};
Date.prototype.format = function(b) {
    var c = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "h+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        S: this.getMilliseconds()
    };
    if (/(y+)/.test(b)) {
        b = b.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var a in c) {
        if (new RegExp("(" + a + ")").test(b)) {
            b = b.replace(RegExp.$1, RegExp.$1.length == 1 ? c[a] : ("00" + c[a]).substr(("" + c[a]).length));
        }
    }
    return b;
};
function menuCheck(b) {
    var a = top.$("body").data("menuList");
    if (b == undefined || (a && a[b] == 1)) {
        return true;
    } else {
        common.nomalPrompt("功能未开启，请联系管理员");
        return false;
    }

}

