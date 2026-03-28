import AOS from "aos";
import "aos/dist/aos.css";

const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
);

const COUNTER_LOCALE = "id-ID";

const easeOutCubic = (progress) => 1 - Math.pow(1 - progress, 3);

const formatCounterValue = (value, decimals = 0) =>
    new Intl.NumberFormat(COUNTER_LOCALE, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(value);

const setCounterValue = (element, value) => {
    const decimals = Number.parseInt(
        element.dataset.counterDecimals ?? "0",
        10,
    );
    const prefix = element.dataset.counterPrefix ?? "";
    const suffix = element.dataset.counterSuffix ?? "";

    element.textContent = `${prefix}${formatCounterValue(value, decimals)}${suffix}`;
};

const finishCounter = (element) => {
    const endValue = Number.parseFloat(element.dataset.counterEnd ?? "0");

    if (!Number.isFinite(endValue)) {
        return;
    }

    setCounterValue(element, endValue);
    element.dataset.counterAnimated = "true";
};

const animateCounter = (element) => {
    if (element.dataset.counterAnimated === "true") {
        return;
    }

    const endValue = Number.parseFloat(element.dataset.counterEnd ?? "0");
    const startValue = Number.parseFloat(element.dataset.counterStart ?? "0");
    const duration = Number.parseInt(
        element.dataset.counterDuration ?? "850",
        10,
    );

    if (
        !Number.isFinite(endValue) ||
        !Number.isFinite(startValue) ||
        duration <= 0 ||
        prefersReducedMotion.matches
    ) {
        finishCounter(element);
        return;
    }

    const animationStart = performance.now();

    const tick = (timestamp) => {
        const progress = Math.min((timestamp - animationStart) / duration, 1);
        const easedProgress = easeOutCubic(progress);
        const currentValue =
            startValue + (endValue - startValue) * easedProgress;

        setCounterValue(element, currentValue);

        if (progress < 1) {
            requestAnimationFrame(tick);
            return;
        }

        finishCounter(element);
    };

    requestAnimationFrame(tick);
};

const initGuestCounters = () => {
    const counters = [...document.querySelectorAll("[data-counter]")];

    if (counters.length === 0) {
        return;
    }

    if (
        prefersReducedMotion.matches ||
        typeof IntersectionObserver === "undefined"
    ) {
        counters.forEach(finishCounter);
        return;
    }

    const counterObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            });
        },
        {
            threshold: 0.45,
        },
    );

    counters.forEach((counter) => {
        counterObserver.observe(counter);
    });
};

const revealStatGrow = (element) => {
    if (element.dataset.statGrowAnimated === "true") {
        return;
    }

    const delay = Number.parseInt(element.dataset.growDelay ?? "0", 10);

    window.setTimeout(() => {
        element.classList.add("is-visible");
        element.dataset.statGrowAnimated = "true";
    }, Math.max(delay, 0));
};

const initStatGrowAnimation = () => {
    const growItems = [...document.querySelectorAll("[data-stat-grow]")];

    if (growItems.length === 0) {
        return;
    }

    if (prefersReducedMotion.matches) {
        growItems.forEach((item) => item.classList.add("is-visible"));
        return;
    }

    document.body.classList.add("guest-motion-ready");

    if (typeof IntersectionObserver === "undefined") {
        growItems.forEach(revealStatGrow);
        return;
    }

    const growObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                revealStatGrow(entry.target);
                growObserver.unobserve(entry.target);
            });
        },
        {
            threshold: 0.25,
        },
    );

    growItems.forEach((item) => {
        growObserver.observe(item);
    });
};

const initGuestAos = () => {
    if (!document.body?.dataset?.guestLayout) {
        return;
    }

    AOS.init({
        duration: 700,
        easing: "ease-out-cubic",
        once: true,
        offset: 72,
        mirror: false,
        disable: window.matchMedia("(prefers-reduced-motion: reduce)").matches,
    });

    window.addEventListener(
        "load",
        () => {
            AOS.refresh();
        },
        { once: true },
    );
};

const initGuestMotion = () => {
    if (!document.body?.dataset?.guestLayout) {
        return;
    }

    initGuestCounters();
    initStatGrowAnimation();
};

if (document.readyState === "loading") {
    document.addEventListener(
        "DOMContentLoaded",
        () => {
            initGuestAos();
            initGuestMotion();
        },
        { once: true },
    );
} else {
    initGuestAos();
    initGuestMotion();
}
