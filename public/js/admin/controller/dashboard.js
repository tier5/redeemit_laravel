"use strict"; //dashboard

var MyApp = angular.module("dashboard-app", []); 
MyApp.controller('DashboardController',["$timeout", "$scope", function (a, b) {
	
    var c = function(a, b) {
                    return Math.floor(Math.random() * (b - a + 1)) + a;
                },
                d = function(a, b, d) {
                    for (var e = [], f = 0; a > f; ++f) e.push(c(b, d));
                    return e;
                },
                e = function(a, b, d) {
                    for (var e = [], f = 0; a > f; ++f)
                        if (e.length) {
                            var g = 10,
                                h = e[e.length - 1] - g,
                                i = e[e.length - 1] + g;
                            e.push(c(b > h ? b : h, i > d ? d : i));
                        } else e.push(c(b, d));
                    return e;
                };
            b.chartData1 = d(75, 5, 200).join(), b.chartData2 = d(24, 5, 200).join(), b.chartData3 = d(20, 5, 200).join(), b.chartData4 = e(50, 10, 30).join(), b.chartData5 = e(18, 10, 30).join();
            var f = !1;
            a(function() {
                b.$broadcast("chat:receiveMessage", "I have a problem with an order, could you help me out?");
            }, 3e3), b.$on("chat:sendMessage", function() {
                f || (f = !0, a(function() {
                    b.$broadcast("chat:receiveMessage", "Thanks!");
                }, 2e3))
            })
                
}]);