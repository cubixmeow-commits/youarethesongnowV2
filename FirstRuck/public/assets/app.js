(() => {
    'use strict';

    const questions = [
        { key: 'goal', kicker: 'Your goal', title: 'What would make rucking worthwhile?', help: 'Choose the outcome you want to notice first.', options: [['general-fitness', 'Build everyday fitness'], ['outdoor-time', 'Spend more time outside'], ['stress', 'Clear my head'], ['event', 'Prepare for a challenge']] },
        { key: 'weekly_movement', kicker: 'Your baseline', title: 'How often are you active now?', help: 'Walking, workouts, and active work all count.', options: [['rarely', 'Less than once a week'], ['1-2', '1–2 days a week'], ['3-4', '3–4 days a week'], ['5-plus', '5 or more days a week']] },
        { key: 'comfortable_minutes', kicker: 'Comfortable time', title: 'How long can you walk comfortably?', help: 'Choose a duration that does not leave you overly sore the next day.', options: [['20', 'About 20 minutes'], ['30', 'About 30 minutes'], ['45', 'About 45 minutes'], ['60', 'An hour or more']] },
        { key: 'equipment', kicker: 'Your gear', title: 'What will you carry?', help: 'A regular backpack works well for a first ruck.', options: [['backpack', 'A regular backpack'], ['ruck', 'A purpose-built rucksack'], ['vest', 'A weighted vest'], ['none', 'I still need equipment']] },
        { key: 'available_load', kicker: 'Available load', title: 'What weight do you have available?', help: 'This sets an equipment ceiling, not a required starting load.', options: [['unweighted', 'No added weight yet'], ['5-lb', 'Up to 5 lb'], ['10-lb', 'Up to 10 lb'], ['15-lb', '15 lb or more']] },
        { key: 'surface', kicker: 'Underfoot', title: 'Which surface feels best?', help: 'We will favor this surface when routes are otherwise similar.', options: [['paved', 'Paved and predictable'], ['compacted', 'Packed gravel or park paths'], ['trail', 'Natural dirt trails'], ['either', 'A mix is fine']] },
        { key: 'hill_comfort', kicker: 'Terrain', title: 'How do hills feel right now?', help: 'Be conservative. We can add climbing after your base feels steady.', options: [['gentle', 'Keep it mostly level'], ['rolling', 'Gentle rolling hills'], ['steep', 'I am comfortable climbing']] },
        { key: 'sessions_per_week', kicker: 'Your week', title: 'How often can you realistically ruck?', help: 'The plan should fit the week you already have.', options: [['1', 'Once a week'], ['2', 'Twice a week'], ['3', 'Three times a week'], ['4', 'Four times a week']] },
        { key: 'route_type', kicker: 'Route shape', title: 'How do you like to explore?', help: 'Out-and-back routes are easier to shorten. Loops feel more complete.', options: [['out-and-back', 'Out and back'], ['loop', 'A loop'], ['either', 'Either works']] },
        { key: 'setting', kicker: 'Route character', title: 'What matters most nearby?', help: 'We will use this to break close matches.', options: [['quiet', 'A quieter route'], ['shade', 'More shade'], ['facilities', 'Water and toilets'], ['scenic', 'The best scenery']] },
        { key: 'body_consideration', kicker: 'Move with care', title: 'Anything you want the plan to protect?', help: 'This does not diagnose an injury. It helps us keep guidance conservative.', options: [['none', 'No current concerns'], ['knees', 'My knees'], ['back', 'My back'], ['feet', 'My feet or ankles']] },
        { key: 'location_label', kicker: 'Your starting area', title: 'Where should we look for routes?', help: 'Use a general area, or share an approximate location from your device.', type: 'location' },
    ];

    const state = {
        step: 0,
        answers: {},
        csrfToken: '',
        profile: null,
        recommendations: [],
        activeRoute: null,
    };

    const screens = [...document.querySelectorAll('[data-screen]')];
    const questionForm = document.querySelector('#question-form');
    const answerList = document.querySelector('#answer-list');
    const errorNode = document.querySelector('#question-error');
    const toast = document.querySelector('.toast');
    const routeDialog = document.querySelector('#route-dialog');
    let lastDialogTrigger = null;
    let toastTimer = null;

    function showScreen(name) {
        screens.forEach((screen) => {
            const active = screen.dataset.screen === name;
            screen.classList.toggle('is-active', active);
            screen.setAttribute('aria-hidden', String(!active));
        });
        window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
        const activeHeading = document.querySelector(`[data-screen="${name}"] h1`);
        if (activeHeading && name !== 'welcome') {
            activeHeading.setAttribute('tabindex', '-1');
            activeHeading.focus({ preventScroll: true });
        }
    }

    function renderQuestion() {
        const question = questions[state.step];
        document.querySelector('#question-kicker').textContent = question.kicker;
        document.querySelector('#question-title').textContent = question.title;
        document.querySelector('#question-help').textContent = question.help;
        document.querySelector('.step-count').textContent = `${state.step + 1} of ${questions.length}`;
        document.querySelector('.progress-track span').style.width = `${((state.step + 1) / questions.length) * 100}%`;
        document.querySelector('[data-back]').style.visibility = state.step === 0 ? 'hidden' : 'visible';
        errorNode.textContent = '';
        answerList.innerHTML = '<legend class="sr-only">Choose one answer</legend>';

        if (question.type === 'location') {
            const wrapper = document.createElement('div');
            wrapper.className = 'location-field';
            wrapper.innerHTML = `
                <label for="location-label">City, neighborhood, or ZIP code</label>
                <input id="location-label" name="location_label" type="text" autocomplete="postal-code" placeholder="For example, Santa Barbara" maxlength="120">
                <button class="secondary-button" type="button" data-use-location>Use my current location</button>`;
            answerList.append(wrapper);
            const input = wrapper.querySelector('input');
            input.value = state.answers.location_label || '';
            wrapper.querySelector('[data-use-location]').addEventListener('click', useCurrentLocation);
            setTimeout(() => input.focus(), 0);
            return;
        }

        question.options.forEach(([value, label], index) => {
            const option = document.createElement('label');
            option.className = 'answer-option';
            const input = document.createElement('input');
            input.type = 'radio';
            input.name = question.key;
            input.value = value;
            input.checked = state.answers[question.key] === value;
            const text = document.createElement('span');
            text.textContent = label;
            option.append(input, text);
            answerList.append(option);
            if (index === 0 && !state.answers[question.key]) setTimeout(() => input.focus(), 0);
        });
    }

    function useCurrentLocation() {
        const button = document.querySelector('[data-use-location]');
        const input = document.querySelector('#location-label');
        if (!navigator.geolocation) {
            errorNode.textContent = 'Location is unavailable in this browser. Enter a city or ZIP code.';
            input.focus();
            return;
        }
        button.disabled = true;
        button.textContent = 'Finding location…';
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                input.value = 'Current location';
                state.answers.latitude = coords.latitude.toFixed(2);
                state.answers.longitude = coords.longitude.toFixed(2);
                button.textContent = 'Location added';
            },
            () => {
                button.disabled = false;
                button.textContent = 'Use my current location';
                errorNode.textContent = 'Location permission was not granted. Enter a city or ZIP code instead.';
                input.focus();
            },
            { enableHighAccuracy: false, timeout: 8000, maximumAge: 300000 },
        );
    }

    async function submitAnswers() {
        showScreen('analysis');
        const statuses = ['Balancing time, terrain, and experience…', 'Setting a conservative starting effort…', 'Comparing routes with your preferences…'];
        const statusNode = document.querySelector('.analysis-status');
        let statusIndex = 0;
        const statusTimer = setInterval(() => {
            statusIndex = Math.min(statusIndex + 1, statuses.length - 1);
            statusNode.textContent = statuses[statusIndex];
        }, 650);

        try {
            const response = await fetch('api.php?action=recommend', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': state.csrfToken },
                body: JSON.stringify({ answers: state.answers }),
            });
            const data = await response.json();
            if (!response.ok || !data.ok) throw new Error(data.error || 'Unable to build your profile.');
            state.profile = data.profile;
            state.recommendations = data.recommendations;
            window.setTimeout(() => {
                clearInterval(statusTimer);
                renderProfile();
                showScreen('profile');
            }, 650);
        } catch (error) {
            clearInterval(statusTimer);
            showScreen('onboarding');
            errorNode.textContent = error.message || 'Unable to save your profile. Check your connection and try again.';
        }
    }

    function renderProfile() {
        document.querySelector('#profile-title').textContent = state.profile.level;
        document.querySelector('#profile-summary').textContent = 'Your first plan favors control, repeatability, and a route you can shorten.';
        document.querySelector('#profile-duration').textContent = `${state.profile.session_minutes} min`;
        document.querySelector('#profile-load').textContent = state.profile.starting_load;
        document.querySelector('#profile-terrain').textContent = state.profile.terrain;
        document.querySelector('#profile-frequency').textContent = state.profile.weekly_frequency;
        document.querySelector('#profile-note').textContent = state.profile.coaching_note;
        document.querySelector('#health-note').textContent = state.profile.health_note;
    }

    function routePath(geometry) {
        if (!Array.isArray(geometry) || geometry.length < 2) return 'M8 68 L25 57 L39 63 L55 49 L72 52 L91 31';
        return geometry.map((point, index) => `${index === 0 ? 'M' : 'L'}${point[0]} ${point[1]}`).join(' ');
    }

    function renderRoutes() {
        const [featured, ...others] = state.recommendations;
        if (!featured) return;
        state.activeRoute = featured;
        document.querySelector('#featured-name').textContent = featured.name;
        document.querySelector('#featured-score').textContent = `${featured.score}%`;
        document.querySelector('#featured-summary').textContent = featured.summary;
        document.querySelector('#featured-route-path').setAttribute('d', routePath(featured.geometry));
        document.querySelector('#featured-metrics').innerHTML = [
            `${featured.distance_km.toFixed(1)} km`,
            `${featured.elevation_gain_m} m climbing`,
            `About ${featured.estimated_minutes} min`,
        ].map((metric) => `<span>${escapeHtml(metric)}</span>`).join('');
        document.querySelector('#featured-reasons').innerHTML = featured.reasons.map((reason) => `<li>${escapeHtml(reason)}</li>`).join('');

        document.querySelector('#route-list').innerHTML = others.map((route, index) => `
            <button class="route-row" type="button" data-route-index="${index + 1}">
                <h3>${escapeHtml(route.name)}</h3>
                <p>${route.distance_km.toFixed(1)} km · ${route.elevation_gain_m} m climbing · ${route.route_type}</p>
                <strong>${route.score}%</strong>
            </button>`).join('');

        document.querySelectorAll('[data-route-index]').forEach((button) => {
            button.addEventListener('click', () => openRoute(state.recommendations[Number(button.dataset.routeIndex)], button));
        });
    }

    function openRoute(route, trigger) {
        state.activeRoute = route;
        lastDialogTrigger = trigger;
        document.querySelector('#route-dialog-title').textContent = route.name;
        document.querySelector('#dialog-summary').textContent = route.summary;
        document.querySelector('#dialog-stats').innerHTML = `
            <div><dt>Distance</dt><dd>${route.distance_km.toFixed(1)} km</dd></div>
            <div><dt>Climbing</dt><dd>${route.elevation_gain_m} m</dd></div>
            <div><dt>Estimate</dt><dd>${route.estimated_minutes} min</dd></div>`;
        routeDialog.showModal();
        document.querySelector('.dialog-close').focus();
    }

    function showToast(message) {
        clearTimeout(toastTimer);
        toast.textContent = message;
        toast.classList.add('is-visible');
        toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 4200);
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[character]);
    }

    async function bootstrap() {
        try {
            const response = await fetch('api.php?action=bootstrap', { headers: { Accept: 'application/json' } });
            const data = await response.json();
            if (!data.ok) return;
            state.csrfToken = data.csrf_token;
            if (data.answers) state.answers = data.answers;
        } catch {
            showToast('The prototype can open, but profile saving is currently unavailable.');
        }
    }

    document.querySelector('[data-start]').addEventListener('click', () => {
        state.step = 0;
        renderQuestion();
        showScreen('onboarding');
    });

    document.querySelector('[data-back]').addEventListener('click', () => {
        if (state.step === 0) return;
        state.step -= 1;
        renderQuestion();
    });

    questionForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const question = questions[state.step];
        let value = '';
        if (question.type === 'location') {
            value = document.querySelector('#location-label').value.trim();
        } else {
            value = questionForm.querySelector(`input[name="${question.key}"]:checked`)?.value || '';
        }
        if (!value) {
            errorNode.textContent = question.type === 'location' ? 'Enter a city or ZIP code, or use your current location.' : 'Choose one answer to continue.';
            const firstControl = questionForm.querySelector('input');
            if (firstControl) {
                firstControl.setAttribute('aria-invalid', 'true');
                firstControl.setAttribute('aria-describedby', 'question-error');
                firstControl.focus();
            }
            return;
        }
        state.answers[question.key] = value;
        if (state.step < questions.length - 1) {
            state.step += 1;
            renderQuestion();
        } else {
            submitAnswers();
        }
    });

    document.querySelector('[data-view-plan]').addEventListener('click', () => {
        renderRoutes();
        showScreen('today');
    });
    document.querySelector('[data-open-route]').addEventListener('click', (event) => openRoute(state.recommendations[0], event.currentTarget));
    document.querySelector('[data-start-ruck]').addEventListener('click', () => {
        routeDialog.close();
        showToast('Tracking is the next prototype slice. Your route plan is ready.');
    });
    document.querySelector('[data-refresh]').addEventListener('click', () => showToast('Live trail refresh will arrive with geographic data integration.'));
    document.querySelector('[data-nav="profile"]').addEventListener('click', () => showScreen('profile'));
    document.querySelectorAll('[data-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.dataset.tab === 'today') return;
            showToast(`${button.textContent.trim()} is planned for a later prototype slice.`);
        });
    });
    routeDialog.addEventListener('close', () => { if (lastDialogTrigger) lastDialogTrigger.focus(); });
    routeDialog.addEventListener('click', (event) => {
        const rect = routeDialog.getBoundingClientRect();
        const inside = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
        if (!inside) routeDialog.close();
    });

    screens.forEach((screen) => screen.setAttribute('aria-hidden', String(!screen.classList.contains('is-active'))));
    bootstrap();
})();
