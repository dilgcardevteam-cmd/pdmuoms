import 'package:flutter/material.dart';

import '../../../../app/assets/app_assets.dart';

class LoginBranding extends StatelessWidget {
  const LoginBranding({super.key});

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Column(
      children: [
        const _SealBadge(),
        const SizedBox(height: 18),
        Text(
          'PDMUOMS',
          textAlign: TextAlign.center,
          style: theme.textTheme.headlineMedium?.copyWith(
            color: const Color(0xFFFFFFFF),
            fontSize: 34,
            fontWeight: FontWeight.w700,
            letterSpacing: 0.3,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          'PDMU Operations Management System',
          textAlign: TextAlign.center,
          style: theme.textTheme.titleMedium?.copyWith(
            color: const Color(0xFFE4EEFF),
            fontWeight: FontWeight.w400,
          ),
        ),
      ],
    );
  }
}

class _SealBadge extends StatelessWidget {
  const _SealBadge();

  @override
  Widget build(BuildContext context) {
    return Image.asset(
      AppAssets.dilgLogo,
      width: 82,
      height: 82,
      fit: BoxFit.contain,
    );
  }
}
