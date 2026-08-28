---
type: implementation-plan
status: owner-decisions-needed
updated: 2026-08-28
area: payments-subscriptions
owners:
  - CuBiX Meow
  - Brut
related-contract: First Build Feature Contract
---

# Stripe Subscription Plan

## Decision

Stripe is the approved payment and subscription provider for the first invite-only beta.

The initial membership price is **$20.00 USD per month**, charged immediately with **no free trial**.

The initial paid beta accepts USD payments from U.S. billing addresses only. Complimentary reviewer access remains an internal entitlement and does not require a Stripe transaction.

The Stripe business is based in California.

Customer-facing Stripe names:

- Product: **You Are The Song Now Membership**
- Card statement descriptor: **YOU ARE THE SONG**

The payment page and billing/help copy must tell customers that `YOU ARE THE SONG` is what they should expect to see on their card statement.

The paywall appears during the user's first creation journey after they have completed onboarding, entered the song, uploaded or selected portrait(s), and configured the image—but before the first generation is submitted or the finished result is delivered. Successful payment unlocks submission and grants the first monthly credit allowance.

Ordinary cancellation takes effect at the end of the current paid billing period. The user keeps access and remaining credits until then. If the user instead chooses permanent account deletion, access ends immediately, future Stripe renewal is cancelled, and the application account and content are deleted immediately.

If a renewal payment fails, the account enters a seven-day grace period. During grace, the user can sign in, view/download/delete existing gallery content, manage the account and update billing, but cannot submit new generations and receives no new monthly credits. Verified payment recovery restores generation access and grants the renewal allowance exactly once.

The owners authorized computer automation to help configure the existing Stripe account properly when the build reaches the appropriate setup stage. This authorization does not itself lift the repository build freeze or authorize live charging before the payment rules, testing and launch gate are complete.

## Read-only account audit

Checked 2026-08-27:

- the existing Stripe account is accessible and live-capable;
- the product catalog contains no active or archived products;
- no pre-existing V2 prices need to be preserved or migrated;
- the account setup guide is partially complete;
- API credentials exist, but no secret or complete key was revealed, copied or recorded.

This is a clean starting point.

## Recommended first-beta architecture

1. One Stripe Product for the YouAreTheSongNow beta membership.
2. One fixed monthly recurring Price in USD initially.
3. Stripe-hosted Checkout for subscription signup.
4. Stripe-hosted Customer Portal for payment-method updates, invoices and cancellation.
5. Signed Stripe webhooks as the authoritative subscription-state feed.
6. The application stores Stripe customer/product/subscription identifiers and normalized subscription state, never card data.
7. Monthly application credits are granted idempotently after the corresponding invoice is confirmed paid.
8. Complimentary reviewer accounts are granted internally and do not require a fake or zero-dollar Stripe subscription.
9. Development uses a Stripe sandbox and test clocks. Live products, prices, webhooks and charges are created only after the freeze and go-live gates allow them.
10. Limit paid-beta Checkout eligibility to supported U.S. billing addresses and USD; reject unsupported paid enrollment clearly before generation.

## First-creation paywall behavior

1. An invited user activates the account and completes the designed onboarding without payment.
2. The user can enter the song, upload/select portrait(s), choose style, quality and orientation, and see the intended credit cost.
3. On the first attempt to generate, an unsubscribed user sees the paywall before any paid AI image-generation work begins.
4. The paywall presents the $20.00 monthly membership and its included credits; there is no free trial.
5. Stripe-hosted Checkout collects payment.
6. The app waits for verified Stripe confirmation, grants the monthly allowance once and resumes the prepared creation without requiring re-entry.
7. Checkout cancellation or failure returns the user to the preserved creation with no generation charge and no lost inputs.

The onboarding and paywall must be designed as one coherent value narrative. Exact screens, copy, proof, animation and measurement require the dedicated onboarding-design workshop.

## Cancellation and deletion

### Cancel subscription

- Set the Stripe subscription to cancel at the end of the paid billing period.
- Keep the account, gallery and remaining credits usable until that date.
- Do not grant another monthly allowance after the final paid period.
- The user may reverse the scheduled cancellation before the period ends if Stripe and the product flow support it.
- After expiration, move the account into the read-only inactive state; do not silently delete it or its content.

### Delete account now

- Treat this as a separate, explicit irreversible action with identity confirmation and a clear warning.
- End application access immediately.
- Cancel the Stripe subscription so it cannot renew.
- Permanently delete the application profile, portraits, generated images, gallery, creative artifacts and remaining credits.
- Do not promise deletion of payment records Stripe or the business must retain for fraud, tax, accounting or legal obligations; disclose that limited exception in the Privacy Policy.
- Immediate account deletion does not create a prorated subscription refund, except when required by the refund exceptions below.

## Failed renewal payment

- Start a seven-calendar-day grace period when Stripe reports that the renewal invoice has failed or requires unresolved payment action.
- Show a clear, non-alarming billing notice with a link to the Stripe Customer Portal.
- During grace, allow sign-in, profile/billing management, gallery viewing, downloads, sharing controls, image deletion and permanent account deletion.
- Pause all new generation submissions immediately, including use of credits remaining from the prior period.
- Do not grant the new monthly credit allowance until the renewal invoice is verified paid.
- Use Stripe's supported retry and customer-notification behavior while reconciling every webhook idempotently.
- When payment succeeds during grace, restore generation access and grant that billing period's monthly allowance exactly once.
- If payment remains unresolved after seven days, move the account into the read-only inactive state.

## Read-only inactive state

This state applies after a cancelled paid period expires or an unresolved failed-renewal grace period ends.

- Keep the account and existing content until the user deletes them under the retention policy.
- Allow sign-in, profile and billing management, gallery viewing, downloads, sharing controls, image deletion and permanent account deletion.
- Block all new generation submissions.
- Expire any unused subscription credits when paid access ends; do not roll them into a later reactivation.
- Show a clear reactivation path through Stripe.
- After verified payment, restore paid access and grant the new billing period's allowance exactly once.
- Do not re-charge or regenerate automatically merely because the user reactivates.

## Refund policy

- Subscription payments are non-refundable and are not prorated, including when the user chooses immediate permanent account deletion.
- Make this rule clear before payment and in the Terms of Service.
- Allow refunds for duplicate charges, confirmed billing errors, unauthorized payments and situations where applicable law requires one.
- Owners/admins may issue an exceptional Stripe refund when documented circumstances justify it; record the reason and resulting account/credit adjustment.
- A provider or technical generation failure refunds/releases the affected generation credits automatically; it does not refund the monthly subscription.
- Subjective dissatisfaction or imperfect portrait resemblance does not create a subscription or generation refund when a usable image was delivered.
- Stripe disputes and chargebacks follow a documented review process and may suspend paid generation while unresolved.

## Preliminary U.S. sales-tax approach

- Business location: California.
- Product form: an online subscription/service with electronically delivered digital images and no tangible media.
- Current California CDTFA guidance says electronically transmitted digital products, including digital images, are generally not taxable; its audit guidance also says SaaS without transfer of tangible personal property is not subject to California sales tax.
- On that preliminary classification, do not automatically add California sales tax merely because the business or customer is in California.
- Before live charging, have a qualified tax professional confirm the exact product classification and whether the business needs any California permit or registration for its complete activities.
- Other states have different rules. Use Stripe Tax or equivalent reporting to monitor economic-nexus thresholds and collect only where the business is registered and obligated to collect.
- Assign the final Stripe product tax code only after that review; do not guess it during sandbox setup.
- Never introduce physical prints, storage media or bundled tangible goods without reopening the tax analysis.

Official California references:

- CDTFA Publication 109, electronically transmitted products: https://www.cdtfa.ca.gov/formspubs/pub109/nontaxable-sales.htm
- CDTFA Audit Manual discussion of SaaS and electronic delivery: https://cdtfa.ca.gov/taxes-and-fees/manuals/am-04.pdf
- CDTFA registration service for permits/accounts: https://cdtfa.ca.gov/services/registration.htm

## Minimum webhook behavior

The server must verify every Stripe signature and safely handle repeated or out-of-order events. At minimum, reconcile:

- Checkout completion;
- subscription creation, update and deletion;
- invoice paid;
- invoice payment failure and action required;
- refunds or disputes that affect access or credits.

Webhook processing must be idempotent. A repeated event must never grant monthly credits twice.

## Separation of responsibilities

Stripe owns:

- payment-method collection;
- recurring charges and invoices;
- billing status and payment failures;
- the hosted billing-management portal.

The application owns:

- invitation eligibility and account access;
- the durable credit ledger;
- monthly credit grants tied to paid invoices;
- complimentary reviewer allowances;
- generation reservations, deductions and technical-failure refunds;
- product access derived from the latest reconciled Stripe state.

## Owner decisions still required

- Monthly included-credit allowance and low/medium/high generation prices.
- Qualified tax confirmation, Stripe tax-code selection and other-state nexus monitoring policy.

## Configuration sequence

1. Settle the owner decisions above.
2. Finish Stripe business/account readiness without exposing credentials.
3. Configure the sandbox Product, monthly Price and Customer Portal.
4. Implement and test Checkout plus signed, idempotent webhooks.
5. Test success, cancellation, payment failure, recovery, refund and duplicate-event scenarios with sandbox tools and test clocks.
6. Complete security and acceptance review.
7. Only after explicit launch authorization, mirror the approved configuration into live mode and perform a controlled end-to-end transaction.

## Official references

- Stripe fixed-price subscriptions with hosted Checkout: https://docs.stripe.com/payments/checkout/build-subscriptions
- Stripe Billing integration and Customer Portal: https://docs.stripe.com/billing/subscriptions/build-subscriptions
- Stripe webhook signature verification: https://docs.stripe.com/webhooks/signature
- Stripe Billing sandbox and test clocks: https://docs.stripe.com/billing/testing
