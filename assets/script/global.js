document.getElementById('loadImage').addEventListener('click', () => {
    const img = document.getElementById('randomImage');
    img.src = `https://picsum.photos/200/300?random=${new Date().getTime()}`;
});
