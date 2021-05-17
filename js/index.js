// IIFE (Immediately Invoked Function Expression)
(function() {
    console.log(`current user id = ${USER_ID}`)

    const sidenav = document.getElementById('sidenav')
    // Select which nav-link should be active based on the current uri
    function updateSidenavLink() {
        let pathname_start = '/' + (location.pathname.split('/', 2)[1])

        for (let nav_link of sidenav.children) {
            if (nav_link.getAttribute('href') === pathname_start)
                nav_link.classList.add('active')
            else
                nav_link.classList.remove('active')
        }
    }
    updateSidenavLink()

    const mainView = document.getElementById('mainView')

    // Set up the MainView templates
    const mainViewLoadingTemplate = document.getElementById('mainViewLoadingTemplate')
    const mainViewErrorTemplate = document.getElementById('mainViewErrorTemplate')
    const mainViewNotFoundTemplate = document.getElementById('mainViewNotFoundTemplate')
    function showMainViewTemplate(template) {
        mainView.innerHTML = template.innerHTML
    }
    showMainViewTemplate(mainViewLoadingTemplate);

    const routes = [
        {path: '/home', view: 'Home'},
        {path: '/messages', view: 'Messages'},
        {path: '/messages/:user_id', view: 'Messages'},
        {path: '/post/:post_id', view: 'Post'},
        {path: '/profile', view: 'Profile'},
        {path: '/profile/:user_id', view: 'Profile'},
        {path: '/settings', view: 'Settings'},
    ]
    // Generate the RegEx patterns to match the paths
    for (const route of routes)
        route.path_regex = pathToRegex(route.path)

    let current_view
    async function loadView() {
        console.log('current pathname: ' + location.pathname)

        let match
        for(const route of routes)
            if (location.pathname.match(route.path_regex)) {
                match = route
                break;
            }

        if (!match) {
            showMainViewTemplate(mainViewNotFoundTemplate)
            return
        }

        try {
            const view_module = await import(`/js/views/${match.view}.js`)
            const params = getParamsFromURI(match.path, location.pathname)
            current_view = new view_module.default(params)

            mainView.innerHTML = await current_view.getHTML();

            // Add a reference to the function so that the inner views can access it
            let functions = {
                navigateTo: navigateTo
            }

            await current_view.init(mainView, functions)
        } catch (e) {
            showMainViewTemplate(mainViewErrorTemplate)
            console.error(e)
        }
    }


    function navigateTo(url) {
        // Add a new entry to the browser's history
        history.pushState({}, '', url)
        updateSidenavLink()
        loadView().then()
    }


    if (location.pathname === '/')
        navigateTo('/home')
    else
        loadView().then()


    // Called when the user goes back on the browser's history
    window.addEventListener('popstate', () => {
        updateSidenavLink()
        loadView().then()
    })


    document.body.addEventListener('click', event => {
        // Make [data-link] links load a new view instead of reloading the page
        if (event.target.matches('[data-link]')) {
            event.preventDefault() // Prevent the link from reloading the page
            navigateTo(event.target.href)
        }

        // Make [data-reload] buttons reload the current view
        if (event.target.matches('[data-reload]'))
            loadView().then()
    })

    // Make the logout button...wait for it...log you out!
    document.getElementById('btnLogout')
        .addEventListener('click', () => {window.location = '/auth/logout.php'})
})();