# SaltLite Components

> Feels like home, just without the salty tears of frustration

The SaltLite Framework is a "batteries-included" PHP framework, modeled on other
modern frameworks like Symfony and Laravel, derived from the original Salt project.
Ideally, it adapts the best core features of Salt without dragging along unnecessary
complexity, technical debt and design decisions we regret. The goal is to provide
users with a robust framework with minimum cognitive overhead from the original
Salt framework, avoiding the pitfalls of bringing in a full-fledged third-party
framework and trying to adapt that to our needs.

This package contains framework-agnostic components for the SaltLite project, which
are intended to be compatible with both the [SaltLite Framework](https://github.com/phoneburner/salt-lite-framework) and the original
Salt project, providing a consistent and reliable foundation for building applications.

### Design & Dependencies

The components are designed to be modular and reusable, and could potentially be
split into their own packages in the future via subtree splitting. We separate
these components from the SaltLite framework to reduce the number of dependencies,
ability to reuse them in other projects, and to allow for easier testing and development.
The number of third party dependencies should be kept to a minimum, and not bound
to "framework implementation details", such as the ORM/database layer, requiring
Redis or RabbitMQ, etc.
