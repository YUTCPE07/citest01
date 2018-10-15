// angular.module('angularDemo').filter('dynamicFilter', function () {
//     return function (products, filterCategories, scope) {
//       var filtered = [];

//       var productFilters = _.filter(filterCategories, function(fc) {
//         return  _.any(fc.options, { 'IsIncluded': true });
//       });

//       _.each(products, function(prod) {
//         var includeProduct = true;
//         _.each(productFilters, function(filter) {
//           var props = _.filter(prod.properties, { 'name': filter.name });
//           if (!_.any(props, function(prop) { return _.any(filter.options, { 'value': prop.value, 'IsIncluded': true }); })) {
//             includeProduct = false;
//           }
//         });
//         if (includeProduct) {
//           filtered.push(prod);
//         }
//       });
//       return filtered;
//     };
//   });