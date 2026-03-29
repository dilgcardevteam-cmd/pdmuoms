import 'package:flutter/material.dart';

import '../../../../app/assets/app_assets.dart';
import '../widgets/login_branding.dart';
import '../widgets/login_form_section.dart';

class LoginPage extends StatelessWidget {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFFDFD),
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            return SingleChildScrollView(
              child: ConstrainedBox(
                constraints: BoxConstraints(minHeight: constraints.maxHeight),
                child: Column(
                  children: [
                    const _LoginHeader(),
                    Transform.translate(
                      offset: const Offset(0, -34),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 28),
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 420),
                          child: const LoginFormSection(),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}

class _LoginHeader extends StatelessWidget {
  const _LoginHeader();

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 420,
      width: double.infinity,
      child: Stack(
        children: [
          Positioned.fill(
            child: ClipPath(
              clipper: _HeaderClipper(),
              child: Stack(
                fit: StackFit.expand,
                children: [
                  DecoratedBox(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: [
                          Color(0xFF2A6DDB),
                          Color(0xFF1C56B2),
                          Color(0xFF3F82EE),
                        ],
                      ),
                    ),
                  ),
                  Opacity(
                    opacity: 0.18,
                    child: Image.asset(
                      AppAssets.loginBackground,
                      fit: BoxFit.cover,
                    ),
                  ),
                  const DecoratedBox(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [Color(0x10FFFFFF), Color(0x18FFFFFF)],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const Positioned.fill(
            child: Padding(
              padding: EdgeInsets.fromLTRB(28, 28, 28, 92),
              child: Align(
                alignment: Alignment.bottomCenter,
                child: LoginBranding(),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _HeaderClipper extends CustomClipper<Path> {
  const _HeaderClipper();

  @override
  Path getClip(Size size) {
    final path = Path()..lineTo(0, size.height - 78);
    path.quadraticBezierTo(
      size.width * 0.18,
      size.height - 122,
      size.width * 0.42,
      size.height - 62,
    );
    path.cubicTo(
      size.width * 0.64,
      size.height - 8,
      size.width * 0.82,
      size.height - 34,
      size.width,
      size.height - 94,
    );
    path.lineTo(size.width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(covariant CustomClipper<Path> oldClipper) => false;
}
