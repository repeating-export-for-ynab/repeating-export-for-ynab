import "./bootstrap";
import "flowbite";

import { Collapse, initTE } from "tw-elements";

initTE({ Collapse });

import * as Sentry from "@sentry/browser";

Sentry.init({
    dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,

    integrations: [
        Sentry.feedbackIntegration({
            // Additional SDK configuration goes in here, for example:
            colorScheme: "system",
        }),
    ],
});
