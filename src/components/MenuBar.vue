<template>
  <div class="col-1 menubar sticky-top">
    <img alt="Vue logo" class="logo" height="90" src="@/assets/logo.png" width="111" />
    <h4>World</h4>
    <div>
      <b><a href="">Kitties</a></b>
    </div>
    <div>
      <b><a href="">Food</a></b>
    </div>
    <div>
      <b><a href="">Toys</a></b>
    </div>
    <div>
      <b><a href="">Treats</a></b>
    </div>
    <div>
      <b><a href="">Purreferences</a></b>
    </div>
    <h4>Feline</h4>
    <div>
      <b><a href="">Breeds</a></b>
    </div>
    <div>
      <b><a href="">Paws</a></b>
    </div>
    <div>
      <b><a href="">Noses</a></b>
    </div>
  </div>
</template>

<style scoped>
.menubar {
  background-color: #131313;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  height: fit-content;
  line-height: 2.5em;
  margin: 5px;
  text-align: center;
}

.menubar img {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
</style>
