function load(){
    let themeText = document.getElementById('theme');
    let light = document.getElementById('light');
    let dark = document.getElementById('dark');
    let system = document.getElementById('system');


    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
        themeText.innerHTML = dark.innerHTML;
    } else {

        document.documentElement.classList.remove('dark')
        if(!('theme' in localStorage)) {
            themeText.innerHTML = system.innerHTML;
        } else {
            themeText.innerHTML = light.innerHTML;
        }
    }

    light.addEventListener('click', function() {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
        themeText.innerHTML = light.innerHTML;
    });

    dark.addEventListener('click', function() {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
        themeText.innerHTML = dark.innerHTML;
    });

    system.addEventListener('click', function() {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'system';
        themeText.innerHTML = system.innerHTML;
    });
}

export default load
