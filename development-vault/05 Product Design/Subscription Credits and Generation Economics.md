# Subscription Credits and Generation Economics

## Status
Working product/economics decision for the initial YouAreTheSongNow subscription model.

## Core Idea
Use a generous user-facing credit system while keeping a strict internal ceiling on monthly image-generation cost per subscriber.

The initial working subscription price is **$19.99/month**.

Internally, target a maximum of approximately **$15/month in image-generation cost per subscriber**. The user does not need to see the dollar budget. Instead, the app presents a monthly credit balance and translates that balance into understandable approximate image counts.

## Initial Credit Model
A simple starting point is:

- **1,500 credits per month**
- Approximately **$15 maximum internal generation liability**
- Credits renew with the subscription period
- Only successful generations deduct credits; failed generations should not consume the user's allowance

### Example generation costs

| Generation type | Approx. provider cost | User credit cost | Approx. images from 1,500 credits |
| --- | ---: | ---: | ---: |
| Standard image | $0.04 | 4 credits | 375 images |
| Premium image | $0.07 | 7 credits | 214 images |

Users can mix generation types, so actual image count varies according to the models and quality settings they choose.

## User-Facing Presentation
Avoid presenting the internal $15 budget to users. Show credits together with a simple image equivalent so the allowance feels concrete and generous.

Example:

> **1,500 credits**  
> ≈ 214 Premium images or 375 Standard images

For a partially used balance, the app can dynamically show an estimate such as:

> **1,120 credits remaining**  
> About 160–280 images, depending on generation type

The exact wording and range can be refined as the actual generation catalog is finalized.

## Why Credits Instead of a Fixed Image Limit
Credits decouple subscription pricing from any single image model or provider cost. This gives the product flexibility to:

- Offer Standard and Premium generations at different credit costs.
- Add edits, variations, higher-resolution generations, or future media types without redesigning the subscription.
- Change internal provider/model choices as prices improve.
- Adjust credit prices for individual generation modes while preserving the subscription's overall value proposition.
- Keep a predictable maximum generation liability per subscriber.

## Expected Usage Behavior
The model assumes most subscribers will not consume the full monthly allowance. Heavy users may reach or approach the cap, but those users are also likely to be among the most engaged users because image creation itself can become a repeat entertainment loop:

**song → image → variation → save/share → another song → another image**

This makes a generous allowance strategically useful. Casual users preserve margin, while highly engaged users experience the subscription as unusually valuable.

A small percentage of users consistently reaching the monthly ceiling could later justify a higher-priced **Creator/Power User tier** rather than reducing the generosity of the main subscription.

## Product Principle
The visible product promise should emphasize creative freedom and abundance, not infrastructure cost.

Potential positioning:

- **Hundreds of creations every month**
- A clearly visible credit balance
- Approximate image equivalents beside the balance
- No charge for failed generations

The internal accounting system remains responsible for making sure the maximum generation exposure stays near the target budget.

## Initial Decision
For early planning, use:

- **Subscription:** $19.99/month
- **Monthly allowance:** 1,500 credits
- **Internal maximum generation budget:** approximately $15/subscriber/month
- **Standard image:** approximately 4 credits (~$0.04 internal cost)
- **Premium image:** approximately 7 credits (~$0.07 internal cost)
- **Estimated range:** roughly 214–375 images/month if using only these two generation classes

These values are provisional and should be recalibrated using real production API pricing, App Store fees, storage/backend costs, and observed subscriber usage before launch.
