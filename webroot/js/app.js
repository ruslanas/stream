var app = angular.module('blog', ['ngResource']);
app.controller('MainController', ['Post', function(Post) {

    this.title = 'Stream';
    this.posts = Post.query();
    this.loading = false;
    this.showForm = false;

    var self = this;
    this.save = function(post) {
        this.loading = true;
        post.$save(function(res) {
            self.loading = false;
        }, function(res) {
            self.loading = false;
            console.error(res);
        });
    };

    this.create = function(post) {
        var newPost = new Post(post);

        newPost.$save(function(res) {
            self.showForm = false;
        }, function(res) {
            console.error(res);
        });

    };

    this.delete = function(post) {
        post.$delete(function(res) {
            self.posts = self.posts.filter(function(el) {
                return el.id !== res.id;
            });
        }, function(res) {
            console.log(res);
        });
    };
}]);

app.factory('Post', ['$resource', function($resource) {
    return $resource('/posts/:id.json', {id: "@id"}, {
        query: {
            method: 'GET',
            isArray: true
        },
        delete: {
            method: 'DELETE'
        }
    });
}]);
