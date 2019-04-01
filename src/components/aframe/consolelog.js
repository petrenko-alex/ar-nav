/* globals AFRAME */
export default AFRAME.registerComponent('console-log', {
  schema: {
    message: {type: 'string'}
  },
  init: function () {
    console.log(this.data.message);
  }
})